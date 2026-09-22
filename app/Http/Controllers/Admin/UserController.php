<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = User::query()->with('roles');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString()
            ->through(fn (User $user) => [
                ...$user->toArray(),
                'avatar_url' => $user->avatar_path ? URL::temporarySignedRoute('profile.avatar.public', now()->addMinutes(5), ['user' => $user]) : null,
            ]);
        $roles = Role::pluck('name');

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => ['search' => $search ?? ''],
        ]);
    }

    public function create()
    {
        $roles = Role::pluck('name');

        return Inertia::render('Admin/Users/Create', [
            'roles' => $roles,
        ]);
    }

    public function edit(User $user)
    {
        $user->load('roles');
        $roles = Role::pluck('name');
        $connectedApps = DB::table('oauth_access_tokens')
            ->join('oauth_clients', 'oauth_access_tokens.client_id', '=', 'oauth_clients.id')
            ->where('oauth_access_tokens.user_id', $user->id)
            ->where('oauth_access_tokens.revoked', false)
            ->select(
                'oauth_clients.id as client_id',
                'oauth_clients.name as app_name',
                DB::raw('COUNT(oauth_access_tokens.id) as token_count'),
                DB::raw('MAX(oauth_access_tokens.created_at) as last_connected')
            )
            ->groupBy('oauth_clients.id', 'oauth_clients.name')
            ->orderByDesc('last_connected')
            ->get()
            ->map(fn ($app) => [
                'client_id' => $app->client_id,
                'app_name' => $app->app_name,
                'token_count' => $app->token_count,
                'last_connected' => $app->last_connected,
            ]);

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'roles' => $roles,
            'connectedApps' => $connectedApps,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'nullable|string|max:50|unique:users,nik',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'nik' => $request->nik,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nik' => 'nullable|string|max:50|unique:users,nik,'.$user->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|exists:roles,name',
        ]);

        $user->update([
            'nik' => $request->nik,
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index');
    }

    public function destroy(User $user)
    {
        $avatarPath = $user->avatar_path;

        DB::transaction(function () use ($user) {
            DB::table('oauth_access_tokens')
                ->where('user_id', $user->id)
                ->where('revoked', false)
                ->update(['revoked' => true]);

            $user->syncRoles([]);
            $user->delete();
        });

        if ($avatarPath) {
            Storage::disk('s3')->delete($avatarPath);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus');
    }
}
