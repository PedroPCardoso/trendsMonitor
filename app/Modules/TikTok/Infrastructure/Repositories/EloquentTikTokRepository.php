<?php

namespace App\Modules\TikTok\Infrastructure\Repositories;

use App\Modules\Shared\Infrastructure\Models\Trend;
use App\Modules\TikTok\Domain\Entities\TikTokTrend;
use App\Modules\TikTok\Domain\Repositories\TikTokTrendRepositoryInterface;
use Carbon\Carbon;

class EloquentTikTokRepository implements TikTokTrendRepositoryInterface
{
    public function saveBatch(array $trends): void
    {
        $now = Carbon::now();

        foreach ($trends as $trend) {
            Trend::create([
                'platform' => 'tiktok',
                'identifier' => $trend->hashtag,
                'title' => $trend->hashtag,
                'rank' => $trend->rank,
                'metadata' => $trend->metadata,
                'fetched_at' => $now,
            ]);
        }
    }

    public function getLatest(int $limit = 10): array
    {
        $latestFetch = Trend::where('platform', 'tiktok')->max('fetched_at');

        if (!$latestFetch) {
            return [];
        }

        $models = Trend::where('platform', 'tiktok')
            ->where('fetched_at', $latestFetch)
            ->orderBy('rank')
            ->limit($limit)
            ->get();

        return $models->map(fn($model) => new TikTokTrend(
            hashtag: $model->title,
            rank: $model->rank,
            metadata: $model->metadata ?? []
        ))->all();
    }
}
