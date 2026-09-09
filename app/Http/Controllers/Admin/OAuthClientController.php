<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Laravel\Passport\Client;

class OAuthClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::where('owner_id', $request->user()->id)
            ->where('owner_type', get_class($request->user()))
            ->latest()
            ->get()
            ->map(function (Client $client) {
                $client->icon_url = $client->icon_path ? route('admin.clients.icon', $client->id) : null;

                return $client;
            });

        return Inertia::render('Admin/Clients/Index', [
            'clients' => $clients,
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
            'client' => $client,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'redirect' => 'required|url',
            'app_url' => 'nullable|url|max:255',
            'lifecycle_status' => ['required', Rule::in(['development', 'production'])],
        ]);

        $secret = Str::random(40);

        $client = Client::create([
            'id' => (string) Str::uuid(),
            'owner_id' => $request->user()->id,
            'owner_type' => get_class($request->user()),
            'name' => $request->name,
            'app_url' => $request->app_url ?: $this->getAppUrl($request->redirect),
            'lifecycle_status' => $request->lifecycle_status,
            'secret' => $secret,
            'provider' => null,
            'redirect_uris' => [$request->redirect],
            'grant_types' => ['authorization_code', 'refresh_token'],
            'revoked' => false,
        ]);

        return redirect()->route('admin.clients.create')->with([
            'new_client' => [
                'id' => $client->id,
                'name' => $request->name,
                'secret' => $secret,
            ],
        ]);
    }

    public function update(Request $request, $clientId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'redirect' => 'required|url',
            'app_url' => 'nullable|url|max:255',
            'lifecycle_status' => ['required', Rule::in(['development', 'production'])],
        ]);

        $client = Client::where('id', $clientId)->first();

        if ($client) {
            $client->update([
                'name' => $request->name,
                'app_url' => $request->app_url ?: $this->getAppUrl($request->redirect),
                'lifecycle_status' => $request->lifecycle_status,
                'redirect_uris' => [$request->redirect],
            ]);
        }

        return redirect()->route('admin.clients.index');
    }

    public function updateIcon(Request $request, $clientId)
    {
        $request->validate([
            'icon' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ]);

        $client = Client::where('id', $clientId)->firstOrFail();
        $path = $request->file('icon')->store('app-icons/'.$client->id, 's3');

        if ($client->icon_path) {
            Storage::disk('s3')->delete($client->icon_path);
        }

        $client->update(['icon_path' => $path]);

        return back()->with('success', 'Icon aplikasi berhasil diperbarui.');
    }

    public function icon($clientId)
    {
        $client = Client::where('id', $clientId)->firstOrFail();

        abort_unless($client->icon_path, 404);

        return redirect(Storage::disk('s3')->temporaryUrl($client->icon_path, now()->addMinutes(5)));
    }

    private function getAppUrl(string $redirect): string
    {
        $parts = parse_url($redirect);

        return ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? $redirect).(isset($parts['port']) ? ':'.$parts['port'] : '');
    }

    public function destroy($clientId)
    {
        $client = Client::where('id', $clientId)->first();

        if ($client) {
            $client->delete();
        }

        return redirect()->route('admin.clients.index');
    }

    public function showSecret($clientId)
    {
        return response()->json([
            'error' => 'Client Secret telah dienkripsi dan tidak bisa ditampilkan. Gunakan fitur Regenerate Secret untuk membuat secret baru.',
        ], 400);
    }

    public function regenerateSecret(Request $request, $clientId)
    {
        $client = Client::where('id', $clientId)->firstOrFail();

        $newSecret = Str::random(40);
        $client->secret = $newSecret;
        $client->save();

        return response()->json([
            'secret' => $newSecret,
        ]);
    }
}
