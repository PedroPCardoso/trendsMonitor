<?php

namespace App\Modules\Instagram\Infrastructure\Repositories;

use App\Modules\Shared\Infrastructure\Models\Trend;
use App\Modules\Instagram\Domain\Entities\InstagramTrend;
use App\Modules\Instagram\Domain\Repositories\InstagramTrendRepositoryInterface;
use Carbon\Carbon;

class EloquentInstagramRepository implements InstagramTrendRepositoryInterface
{
    public function saveBatch(array $trends): void
    {
        $now = Carbon::now();

        foreach ($trends as $trend) {
            Trend::create([
                'platform' => 'instagram',
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
        $latestFetch = Trend::where('platform', 'instagram')->max('fetched_at');

        if (!$latestFetch) {
            return [];
        }

        $models = Trend::where('platform', 'instagram')
            ->where('fetched_at', $latestFetch)
            ->orderBy('rank')
            ->limit($limit)
            ->get();

        return $models->map(fn($model) => new InstagramTrend(
            hashtag: $model->title,
            rank: $model->rank,
            metadata: $model->metadata ?? []
        ))->all();
    }
}
