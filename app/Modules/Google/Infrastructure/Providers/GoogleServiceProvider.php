<?php

namespace App\Modules\Google\Infrastructure\Providers;

use App\Modules\Google\Domain\Repositories\GoogleTrendRepositoryInterface;
use App\Modules\Google\Infrastructure\Repositories\EloquentGoogleRepository;
use Illuminate\Support\ServiceProvider;

class GoogleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            GoogleTrendRepositoryInterface::class,
            EloquentGoogleRepository::class
        );
    }
}
