<?php

namespace App\Modules\TikTok\Infrastructure\Providers;

use App\Modules\TikTok\Domain\Repositories\TikTokTrendRepositoryInterface;
use App\Modules\TikTok\Infrastructure\Repositories\EloquentTikTokRepository;
use Illuminate\Support\ServiceProvider;

class TikTokServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TikTokTrendRepositoryInterface::class,
            EloquentTikTokRepository::class
        );
    }
}
