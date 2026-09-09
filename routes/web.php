<?php

use App\Http\Controllers\Admin\OAuthClientController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ConnectedAppController;
use App\Http\Controllers\ProfileController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

// Route untuk Guest (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

// Route untuk Authenticated User (Sudah Login)
Route::middleware('auth')->group(function () {
    Route::get('/', function (Request $request) {
        $user = $request->user();

        $getAppUrl = function (?string $appUrl, string $redirectUris): string {
            if ($appUrl) {
                return $appUrl;
            }

            $redirect = json_decode($redirectUris, true)[0] ?? $redirectUris;
            $parts = parse_url($redirect);

            return ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? $redirect).(isset($parts['port']) ? ':'.$parts['port'] : '');
        };

        $connectedApps = DB::table('oauth_access_tokens')
            ->join('oauth_clients', 'oauth_access_tokens.client_id', '=', 'oauth_clients.id')
            ->where('oauth_access_tokens.user_id', $user->id)
            ->where('oauth_access_tokens.revoked', false)
            ->where('oauth_clients.lifecycle_status', 'production')
            ->select(
                'oauth_clients.id as id',
                'oauth_clients.name',
                DB::raw('MAX(oauth_clients.lifecycle_status) as lifecycle_status'),
                DB::raw('MAX(oauth_clients.icon_path) as icon_path'),
                DB::raw('MAX(oauth_clients.app_url) as app_url'),
                DB::raw('MAX(oauth_clients.redirect_uris) as redirect_uris'),
                DB::raw('COUNT(oauth_access_tokens.id) as token_count'),
                DB::raw('MAX(oauth_access_tokens.created_at) as last_connected'),
            )
            ->groupBy('oauth_clients.id', 'oauth_clients.name')
            ->get()
            ->map(fn ($app) => [
                'id' => $app->id,
                'name' => $app->name,
                'icon_url' => $app->icon_path ? route('aplikasi-terhubung.icon', $app->id) : null,
                'category' => 'OAuth2 App',
                'connectedAt' => Carbon::parse($app->last_connected)->translatedFormat('d M Y'),
                'status' => 'Production',
                'url' => $getAppUrl($app->app_url, $app->redirect_uris),
                'token_count' => $app->token_count,
            ]);

        return Inertia::render('Home', [
            'auth' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar_url' => $user->avatar_path ? route('profile.avatar') : null,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                ],
            ],
            'summary' => [
                'activeApps' => $connectedApps->count(),
                'activeSessions' => 1,
                'tokensIssued' => $connectedApps->count(),
                'rolesCount' => $user->roles()->count(),
            ],
            'connectedApps' => $connectedApps,
        ]);
    })->name('dashboard');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::get('/profile/avatar', [ProfileController::class, 'avatar'])->name('profile.avatar');

    Route::get('/profile', function (Request $request) {
        $user = $request->user();

        return Inertia::render('Profile', [
            'auth' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar_url' => $user->avatar_path ? route('profile.avatar') : null,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                ],
            ],
        ]);
    })->name('profile');

    Route::get('/token-aktif', [ConnectedAppController::class, 'index'])->name('token-aktif');
    Route::get('/aplikasi-terhubung', [ConnectedAppController::class, 'summary'])->name('aplikasi-terhubung');
    Route::get('/aplikasi-terhubung/{clientId}/icon', [ConnectedAppController::class, 'icon'])->name('aplikasi-terhubung.icon');
    Route::get('/aplikasi-terhubung/{clientId}', [ConnectedAppController::class, 'appTokens'])->name('aplikasi-terhubung.tokens');
    Route::post('/aplikasi-terhubung/{clientId}/revoke-all', [ConnectedAppController::class, 'revokeAll'])->name('aplikasi-terhubung.revoke-all');
    Route::get('/security', function () {
        return Inertia::render('Security');
    })->name('security');
    Route::post('/token-aktif/{tokenId}/revoke', [ConnectedAppController::class, 'revoke'])->name('token-aktif.revoke');

    Route::get('/password/change', function (Request $request) {
        return Inertia::render('Auth/ChangePassword');
    })->name('password.change');

    Route::post('/password/change', function (Request $request) {
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($request) {
                if (! Hash::check($value, $request->user()->password)) {
                    $fail('Password saat ini tidak sesuai.');
                }
            }],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile')->with('success', 'Password berhasil diubah!');
    });
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // OAuth Clients CRUD
    Route::post('clients/{client}/icon', [OAuthClientController::class, 'updateIcon'])->name('clients.icon.update');
    Route::get('clients/{client}/icon', [OAuthClientController::class, 'icon'])->name('clients.icon');
    Route::resource('clients', OAuthClientController::class);

    // Client Secret management
    Route::get('clients/{client}/secret', [OAuthClientController::class, 'showSecret'])->name('clients.secret');
    Route::post('clients/{client}/regenerate-secret', [OAuthClientController::class, 'regenerateSecret'])->name('clients.regenerate-secret');

    // User Management CRUD
    Route::resource('users', UserController::class);

    // Roles & Permissions CRUD
    Route::resource('roles', RoleController::class);
});
