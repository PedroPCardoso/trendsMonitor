<?php

namespace App\Modules\Instagram\Infrastructure\Jobs;

use App\Modules\Instagram\Domain\Repositories\InstagramTrendRepositoryInterface;
use App\Modules\Instagram\Infrastructure\Providers\PythonScraperInstagramProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchInstagramTrendsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 180;

    public function handle(
        PythonScraperInstagramProvider $provider,
        InstagramTrendRepositoryInterface $repository
    ): void {
        Log::info('Starting Instagram trends fetch...');

        try {
            $trends = $provider->fetchTrends();
            
            if (empty($trends)) {
                Log::warning('No Instagram trends found via Scraper.');
                return;
            }

            $repository->saveBatch($trends);
            
            Log::info('Instagram trends fetched and saved successfully.', ['count' => count($trends)]);
        } catch (\Throwable $e) {
            Log::error('Error fetching Instagram trends: ' . $e->getMessage());
            throw $e;
        }
    }
}
