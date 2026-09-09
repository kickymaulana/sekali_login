<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();
        $path = $request->file('avatar')->store('avatars/'.$user->id, 's3');

        if ($user->avatar_path) {
            Storage::disk('s3')->delete($user->avatar_path);
        }

        $user->update(['avatar_path' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function avatar(Request $request)
    {
        $user = $request->user();

        abort_unless($user->avatar_path, 404);

        return redirect(Storage::disk('s3')->temporaryUrl($user->avatar_path, now()->addMinutes(5)));
    }
}
