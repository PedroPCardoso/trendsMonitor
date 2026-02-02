<?php

namespace App\Modules\Google\Infrastructure\Repositories;

use App\Modules\Shared\Infrastructure\Models\Trend;
use App\Modules\Google\Domain\Entities\GoogleTrend;
use App\Modules\Google\Domain\Repositories\GoogleTrendRepositoryInterface;
use Carbon\Carbon;

class EloquentGoogleRepository implements GoogleTrendRepositoryInterface
{
    public function saveBatch(array $trends): void
    {
        $now = Carbon::now();

        foreach ($trends as $trend) {
            Trend::create([
                'platform' => 'google',
                'identifier' => md5($trend->title . $now->timestamp), // Unique ID generation
                'title' => $trend->title,
                'rank' => $trend->rank,
                'metadata' => [
                    'entity_names' => $trend->entityNames,
                ],
                'fetched_at' => $now,
            ]);
        }
    }

    public function getLatest(int $limit = 10): array
    {
        $latestFetch = Trend::where('platform', 'google')->max('fetched_at');

        if (!$latestFetch) {
            return [];
        }

        $models = Trend::where('platform', 'google')
            ->where('fetched_at', $latestFetch)
            ->orderBy('rank')
            ->limit($limit)
            ->get();

        return $models->map(fn($model) => new GoogleTrend(
            title: $model->title,
            entityNames: $model->metadata['entity_names'] ?? [],
            rank: $model->rank,
            fetchedAt: new \DateTimeImmutable($model->fetched_at)
        ))->all();
    }
}
