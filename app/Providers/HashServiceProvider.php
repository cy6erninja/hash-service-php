<?php

namespace App\Providers;

use App\Services\HashService;
use App\Services\HashServiceInterface;
use Illuminate\Support\ServiceProvider;

class HashServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(HashServiceInterface::class, function () {
            return new HashService();
        });
    }
}
