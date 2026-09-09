<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ConnectedAppController extends Controller
{
    private function getIconUrl(?string $iconPath, string $clientId): string
    {
        return $iconPath ? route('aplikasi-terhubung.icon', $clientId) : '';
    }

    public function icon(string $clientId)
    {
        $iconPath = DB::table('oauth_clients')->where('id', $clientId)->value('icon_path');

        abort_unless($iconPath, 404);

        return redirect(Storage::disk('s3')->temporaryUrl($iconPath, now()->addMinutes(5)));
    }

    private function getAppUrl(?string $appUrl, string $redirectUris): string
    {
        if ($appUrl) {
            return $appUrl;
        }

        $redirect = json_decode($redirectUris, true)[0] ?? $redirectUris;
        $parts = parse_url($redirect);

        return ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? $redirect).(isset($parts['port']) ? ':'.$parts['port'] : '');
    }

    public function index()
    {
        $userId = auth()->id();

        $apps = DB::table('oauth_access_tokens')
            ->join('oauth_clients', 'oauth_access_tokens.client_id', '=', 'oauth_clients.id')
            ->where('oauth_access_tokens.user_id', $userId)
            ->where('oauth_access_tokens.revoked', false)
            ->select(
                'oauth_access_tokens.id as token_id',
                'oauth_clients.id as client_id',
                'oauth_clients.name as app_name',
                'oauth_access_tokens.created_at',
                'oauth_access_tokens.expires_at'
            )
            ->orderByDesc('oauth_access_tokens.created_at')
            ->get()
            ->map(fn ($app) => [
                'token_id' => $app->token_id,
                'client_id' => $app->client_id,
                'app_name' => $app->app_name,
                'created_at' => Carbon::parse($app->created_at)->translatedFormat('d M Y'),
                'expires_at' => $app->expires_at ? Carbon::parse($app->expires_at)->translatedFormat('d M Y') : '-',
            ])
            ->toArray();

        return Inertia::render('Profile/ConnectedApps', [
            'apps' => $apps,
        ]);
    }

    public function summary()
    {
        $userId = auth()->id();

        $apps = DB::table('oauth_access_tokens')
            ->join('oauth_clients', 'oauth_access_tokens.client_id', '=', 'oauth_clients.id')
            ->where('oauth_access_tokens.user_id', $userId)
            ->where('oauth_access_tokens.revoked', false)
            ->select(
                'oauth_clients.id as client_id',
                'oauth_clients.name as app_name',
                'oauth_clients.icon_path',
                'oauth_clients.lifecycle_status',
                'oauth_clients.app_url',
                'oauth_clients.redirect_uris',
                DB::raw('COUNT(oauth_access_tokens.id) as token_count'),
                DB::raw('MAX(oauth_access_tokens.created_at) as last_connected')
            )
            ->groupBy('oauth_clients.id', 'oauth_clients.name', 'oauth_clients.icon_path', 'oauth_clients.lifecycle_status', 'oauth_clients.app_url', 'oauth_clients.redirect_uris')
            ->orderByDesc('last_connected')
            ->paginate(8)
            ->through(fn ($app) => [
                'client_id' => $app->client_id,
                'app_name' => $app->app_name,
                'icon_url' => $this->getIconUrl($app->icon_path, $app->client_id) ?: null,
                'lifecycle_status' => $app->lifecycle_status,
                'url' => $this->getAppUrl($app->app_url, $app->redirect_uris),
                'token_count' => $app->token_count,
                'last_connected' => Carbon::parse($app->last_connected)->translatedFormat('d M Y'),
            ]);

        return Inertia::render('Profile/ConnectedAppsSummary', [
            'apps' => $apps,
        ]);
    }

    public function appTokens(string $clientId)
    {
        $app = DB::table('oauth_clients')
            ->where('id', $clientId)
            ->select('id', 'name')
            ->first();

        if (! $app) {
            abort(404);
        }

        $apps = DB::table('oauth_access_tokens')
            ->where('user_id', auth()->id())
            ->where('client_id', $clientId)
            ->where('revoked', false)
            ->select('id as token_id', 'created_at', 'expires_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($token) => [
                'token_id' => $token->token_id,
                'created_at' => Carbon::parse($token->created_at)->translatedFormat('d M Y'),
                'expires_at' => $token->expires_at ? Carbon::parse($token->expires_at)->translatedFormat('d M Y') : '-',
            ])
            ->toArray();

        return Inertia::render('Profile/AppTokens', [
            'app' => [
                'id' => $app->id,
                'name' => $app->name,
            ],
            'tokens' => $apps,
        ]);
    }

    public function revokeAll(string $clientId)
    {
        $tokenIds = DB::table('oauth_access_tokens')
            ->where('user_id', auth()->id())
            ->where('client_id', $clientId)
            ->where('revoked', false)
            ->pluck('id');

        if ($tokenIds->isEmpty()) {
            return redirect()->route('aplikasi-terhubung.tokens', $clientId)->with('error', 'Tidak ada token aktif untuk aplikasi ini.');
        }

        DB::table('oauth_access_tokens')
            ->whereIn('id', $tokenIds)
            ->where('user_id', auth()->id())
            ->update(['revoked' => true]);

        DB::table('oauth_refresh_tokens')
            ->whereIn('access_token_id', $tokenIds)
            ->update(['revoked' => true]);

        return redirect()->route('aplikasi-terhubung.tokens', $clientId)->with('success', 'Semua token aktif berhasil dicabut');
    }

    public function revoke($tokenId)
    {
        $token = DB::table('oauth_access_tokens')
            ->where('id', $tokenId)
            ->where('user_id', auth()->id())
            ->where('revoked', false)
            ->first();

        if (! $token) {
            return redirect()->route('token-aktif')->with('error', 'Token aktif tidak ditemukan.');
        }

        DB::table('oauth_access_tokens')
            ->where('id', $tokenId)
            ->where('user_id', auth()->id())
            ->update(['revoked' => true]);

        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $tokenId)
            ->update(['revoked' => true]);

        return redirect()->route('token-aktif')->with('success', 'Token aktif berhasil dicabut');
    }
}
