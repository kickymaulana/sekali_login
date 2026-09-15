<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return response()->json([
        'id' => $request->user()->id,
        'nik' => $request->user()->nik,
        'name' => $request->user()->name,
        'email' => $request->user()->email,
        'avatar_url' => $request->user()->avatar_path ? URL::temporarySignedRoute('profile.avatar.public', now()->addDays(30), ['user' => $request->user()]) : null,
    ]);
});
