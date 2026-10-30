<?php

namespace Mindigo\Auth\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {

        Route::middleware(['web'])->group(__DIR__.'/../Routes/web.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'Mindigo-auth');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'Mindigo-auth');
    }
}
