<?php

namespace App\Modules\Google\Infrastructure\Jobs;

use App\Modules\Google\Domain\Repositories\GoogleTrendRepositoryInterface;
use App\Modules\Google\Infrastructure\Providers\PyTrendsProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchGoogleTrendsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(
        PyTrendsProvider $provider,
        GoogleTrendRepositoryInterface $repository
    ): void {
        Log::info('Starting Google trends fetch...');

        try {
            $trends = $provider->fetchTrends();
            
            if (empty($trends)) {
                Log::warning('No Google trends found via PyTrends.');
                return;
            }

            $repository->saveBatch($trends);
            
            Log::info('Google trends fetched and saved successfully.', ['count' => count($trends)]);
        } catch (\Throwable $e) {
            Log::error('Error fetching Google trends: ' . $e->getMessage());
            throw $e;
        }
    }
}
