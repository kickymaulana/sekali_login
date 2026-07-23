<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'nik' => ['required'],
            'password' => ['required'],
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (Auth::attempt(['nik' => $request->nik, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();

            // 🟢 Ambil URL intended (tujuan awal user sebelum di-intercept halaman login)
            $redirectUrl = session()->pull('url.intended', route('dashboard'));

            // 🟢 Gunakan Inertia::location untuk memicu Full Browser Redirect (bukan AJAX)
            return Inertia::location($redirectUrl);
        }

        return back()->withErrors([
            'nik' => 'NIK atau password yang Anda masukkan salah.',
        ])->onlyInput('nik');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
