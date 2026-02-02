<?php

namespace App\Modules\YouTube\Infrastructure\Jobs;

use App\Modules\YouTube\Domain\Repositories\YouTubeTrendRepositoryInterface;
use App\Modules\YouTube\Infrastructure\Providers\YouTubeDataProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchYouTubeTrendsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(
        YouTubeDataProvider $provider,
        YouTubeTrendRepositoryInterface $repository
    ): void {
        Log::info('Starting YouTube trends fetch...');

        try {
            $trends = $provider->fetchTrends();
            
            if (empty($trends)) {
                Log::warning('No YouTube trends found.');
                return;
            }

            $repository->saveBatch($trends);
            
            Log::info('YouTube trends fetched and saved successfully.', ['count' => count($trends)]);
        } catch (\Throwable $e) {
            Log::error('Error fetching YouTube trends: ' . $e->getMessage());
            throw $e;
        }
    }
}
