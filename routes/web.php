<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\OAuthClientController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;


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
                'activeApps' => 3,       // Aplikasi terhubung
                'activeSessions' => 1,   // Sesi aktif
                'tokensIssued' => 12,    // Token diterbitkan
                'rolesCount' => $user->roles()->count(),
            ],
            'connectedApps' => [
                [
                    'id' => 1,
                    'name' => 'Aplikasi Klien PHP Native',
                    'category' => 'Web App (OAuth2)',
                    'connectedAt' => '22 Jul 2026',
                    'status' => 'Active',
                    'icon' => 'code-json',
                ],
            ],
        ]);
    })->name('dashboard');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // OAuth Clients CRUD
    Route::resource('clients', OAuthClientController::class)->except(['create', 'edit']);

    // User Management CRUD
    Route::resource('users', UserController::class)->except(['create', 'edit']);

    // Roles & Permissions CRUD
    Route::resource('roles', RoleController::class)->except(['create', 'edit']);
});
