<?php

namespace App\Modules\YouTube\Infrastructure\Repositories;

use App\Modules\Shared\Infrastructure\Models\Trend;
use App\Modules\YouTube\Domain\Entities\YouTubeTrend;
use App\Modules\YouTube\Domain\Repositories\YouTubeTrendRepositoryInterface;
use Carbon\Carbon;

class EloquentYouTubeRepository implements YouTubeTrendRepositoryInterface
{
    public function saveBatch(array $trends): void
    {
        $now = Carbon::now();

        foreach ($trends as $index => $trend) {
            Trend::updateOrCreate(
                [
                    'platform' => 'youtube',
                    'identifier' => $trend->videoId,
                    'fetched_at' => $now->toDateTimeString(), // Simple strategy: unique fetch per time
                ],
                [
                    'title' => $trend->title,
                    'description' => $trend->description,
                    'thumbnail_url' => $trend->thumbnailUrl,
                    'rank' => $index + 1,
                    'metadata' => [
                        'view_count' => $trend->viewCount,
                        'like_count' => $trend->likeCount,
                        'channel_title' => $trend->channelTitle,
                        'published_at' => $trend->publishedAt->format('Y-m-d H:i:s'),
                        'tags' => $trend->tags,
                    ],
                ]
            );
        }
    }

    public function getLatest(int $limit = 10): array
    {
        // Logic to get the latest batch
        $latestFetch = Trend::where('platform', 'youtube')->max('fetched_at');

        if (!$latestFetch) {
            return [];
        }

        $models = Trend::where('platform', 'youtube')
            ->where('fetched_at', $latestFetch)
            ->orderBy('rank')
            ->limit($limit)
            ->get();

        return $models->map(fn($model) => new YouTubeTrend(
            videoId: $model->identifier,
            title: $model->title,
            description: $model->description,
            thumbnailUrl: $model->thumbnail_url,
            channelTitle: $model->metadata['channel_title'] ?? '',
            viewCount: $model->metadata['view_count'] ?? 0,
            likeCount: $model->metadata['like_count'] ?? 0,
            publishedAt: new \DateTimeImmutable($model->metadata['published_at']),
            tags: $model->metadata['tags'] ?? []
        ))->all();
    }
}
