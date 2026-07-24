<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\OAuthClientController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\ConnectedAppController;


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

        $connectedApps = DB::table('oauth_access_tokens')
            ->join('oauth_clients', 'oauth_access_tokens.client_id', '=', 'oauth_clients.id')
            ->where('oauth_access_tokens.user_id', $user->id)
            ->where('oauth_access_tokens.revoked', false)
            ->select(
                'oauth_access_tokens.id as id',
                'oauth_clients.name',
                'oauth_access_tokens.created_at',
            )
            ->get()
            ->map(fn($app) => [
                'id' => $app->id,
                'name' => $app->name,
                'category' => 'OAuth2 App',
                'connectedAt' => \Carbon\Carbon::parse($app->created_at)->format('d M Y'),
                'status' => 'Active',
                'icon' => 'code-json',
            ]);

        return Inertia::render('Home', [
            'auth' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
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

    Route::get('/profile', function (Request $request) {
        $user = $request->user();

        return Inertia::render('Profile', [
            'auth' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                ],
            ],
        ]);
    })->name('profile');

    Route::get('/connected-apps', [ConnectedAppController::class, 'index'])->name('connected-apps');
    Route::get('/security', function () { return Inertia::render('Security'); })->name('security');
    Route::post('/connected-apps/{tokenId}/revoke', [ConnectedAppController::class, 'revoke'])->name('connected-apps.revoke');

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
    Route::resource('clients', OAuthClientController::class);

    // Client Secret management
    Route::get('clients/{client}/secret', [OAuthClientController::class, 'showSecret'])->name('clients.secret');
    Route::post('clients/{client}/regenerate-secret', [OAuthClientController::class, 'regenerateSecret'])->name('clients.regenerate-secret');

    // User Management CRUD
    Route::resource('users', UserController::class);

    // Roles & Permissions CRUD
    Route::resource('roles', RoleController::class);
});
