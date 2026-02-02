<?php

namespace App\Modules\YouTube\Infrastructure\Providers;

use App\Modules\YouTube\Domain\Repositories\YouTubeTrendRepositoryInterface;
use App\Modules\YouTube\Infrastructure\Repositories\EloquentYouTubeRepository;
use Illuminate\Support\ServiceProvider;

class YouTubeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            YouTubeTrendRepositoryInterface::class,
            EloquentYouTubeRepository::class
        );
    }
}
