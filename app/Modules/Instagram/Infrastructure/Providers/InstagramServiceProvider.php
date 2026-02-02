<?php

namespace App\Modules\Instagram\Infrastructure\Providers;

use App\Modules\Instagram\Domain\Repositories\InstagramTrendRepositoryInterface;
use App\Modules\Instagram\Infrastructure\Repositories\EloquentInstagramRepository;
use Illuminate\Support\ServiceProvider;

class InstagramServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            InstagramTrendRepositoryInterface::class,
            EloquentInstagramRepository::class
        );
    }
}
