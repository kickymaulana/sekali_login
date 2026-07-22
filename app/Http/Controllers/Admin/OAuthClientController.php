<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Passport\Client;
use Inertia\Inertia;

class OAuthClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::where('owner_id', $request->user()->id)
            ->where('owner_type', get_class($request->user()))
            ->latest()
            ->get();

        return Inertia::render('Admin/Clients/Index', [
            'clients' => $clients
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Clients/Create');
    }

    public function edit($clientId)
    {
        $client = Client::where('id', $clientId)->firstOrFail();

        return Inertia::render('Admin/Clients/Edit', [
            'client' => $client
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'redirect' => 'required|url',
        ]);

        // 🟢 Buat Client langsung via Model Client agar aman untuk polymorphic owner
        Client::create([
            'id' => (string) Str::uuid(),
            'owner_id' => $request->user()->id,
            'owner_type' => get_class($request->user()),
            'name' => $request->name,
            'secret' => Str::random(40),
            'provider' => null,
            'redirect_uris' => [$request->redirect], // Laravel Passport v12+ menyimpan array URL
            'grant_types' => ['authorization_code', 'refresh_token'],
            'revoked' => false,
        ]);

        return redirect()->route('admin.clients.index')->with('success', 'OAuth Client berhasil dibuat!');
    }

    public function update(Request $request, $clientId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'redirect' => 'required|url',
        ]);

        $client = Client::where('id', $clientId)->first();

        if ($client) {
            $client->update([
                'name' => $request->name,
                'redirect_uris' => [$request->redirect],
            ]);
        }

        return redirect()->route('admin.clients.index');
    }

    public function destroy($clientId)
    {
        $client = Client::where('id', $clientId)->first();

        if ($client) {
            $client->delete();
        }

        return redirect()->route('admin.clients.index');
    }
}
