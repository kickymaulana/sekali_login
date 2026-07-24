<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
                'oauth_access_tokens.id as token_id',
                'oauth_clients.name as app_name',
                'oauth_access_tokens.created_at',
                'oauth_access_tokens.expires_at'
            )
            ->orderBy('oauth_access_tokens.created_at', 'desc')
            ->get()
            ->toArray();

        return Inertia::render('Profile/ConnectedApps', [
            'apps' => $apps,
        ]);
    }

    public function revoke($tokenId)
    {
        DB::table('oauth_access_tokens')
            ->where('id', $tokenId)
            ->where('user_id', auth()->id())
            ->update(['revoked' => true]);

        return redirect()->route('connected-apps')->with('success', 'Aplikasi berhasil dicabut aksesnya');
    }
}
