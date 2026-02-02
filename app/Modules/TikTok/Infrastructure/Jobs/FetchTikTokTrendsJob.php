<?php

namespace App\Modules\TikTok\Infrastructure\Jobs;

use App\Modules\TikTok\Domain\Repositories\TikTokTrendRepositoryInterface;
use App\Modules\TikTok\Infrastructure\Providers\PythonScraperTikTokProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchTikTokTrendsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 180; // Allow more time for scraping

    public function handle(
        PythonScraperTikTokProvider $provider,
        TikTokTrendRepositoryInterface $repository
    ): void {
        Log::info('Starting TikTok trends fetch...');

        try {
            $trends = $provider->fetchTrends();
            
            if (empty($trends)) {
                Log::warning('No TikTok trends found via Scraper.');
                return;
            }

            $repository->saveBatch($trends);
            
            Log::info('TikTok trends fetched and saved successfully.', ['count' => count($trends)]);
        } catch (\Throwable $e) {
            Log::error('Error fetching TikTok trends: ' . $e->getMessage());
            throw $e;
        }
    }
}
