<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AuthorizationService;

class AuthorizationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
          $this->app->singleton('authorization', function ($app) {
            return new AuthorizationService();
        });
    }
    public function boot(): void
    {
        
    }
}
