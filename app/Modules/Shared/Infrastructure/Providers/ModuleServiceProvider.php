<?php

namespace App\Modules\Shared\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register module-specific bindings here
        $this->app->register(\App\Modules\YouTube\Infrastructure\Providers\YouTubeServiceProvider::class);
        $this->app->register(\App\Modules\Google\Infrastructure\Providers\GoogleServiceProvider::class);
        $this->app->register(\App\Modules\TikTok\Infrastructure\Providers\TikTokServiceProvider::class);
        $this->app->register(\App\Modules\Instagram\Infrastructure\Providers\InstagramServiceProvider::class);
        // ...
        // $this->app->register(InstagramServiceProvider::class);
        // ...
    }

    public function boot(): void
    {
        // Boot module routes/views if needed
    }
}
