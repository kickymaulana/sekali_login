<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ConnectedAppController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $apps = DB::table('oauth_access_tokens')
            ->join('oauth_clients', 'oauth_access_tokens.client_id', '=', 'oauth_clients.id')
            ->where('oauth_access_tokens.user_id', $userId)
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
                'last_connected' => Carbon::parse($app->last_connected)->translatedFormat('d M Y'),
            ])
            ->toArray();

        return Inertia::render('Profile/ConnectedApps', [
            'apps' => $apps,
        ]);
    }

    public function revoke($clientId)
    {
        DB::table('oauth_access_tokens')
            ->where('client_id', $clientId)
            ->where('user_id', auth()->id())
            ->update(['revoked' => true]);

        return redirect()->route('connected-apps')->with('success', 'Akses aplikasi berhasil dicabut');
    }
}
