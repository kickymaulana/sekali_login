<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.url')) {
            URL::forceRootUrl(config('app.url'));
        }
        Passport::authorizationView(
            fn ($parameters) => Inertia::render('Auth/OAuth/Authorize', [
                'request' => $parameters['request'],
                'authToken' => $parameters['authToken'],
                'client' => $parameters['client'],
                'user' => $parameters['user'],
                'scopes' => $parameters['scopes'],
            ])
        );
        // 🟢 Daftarkan View Otorisasi MCP untuk Passport
        if (class_exists(Passport::class)) {
            Passport::authorizationView(function ($parameters) {
                return view('mcp.authorize', $parameters);
            });
        }
    }
}
