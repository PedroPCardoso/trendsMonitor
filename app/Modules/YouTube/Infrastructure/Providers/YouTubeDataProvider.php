<?php

namespace App\Modules\YouTube\Infrastructure\Providers;

use App\Modules\YouTube\Domain\Entities\YouTubeTrend;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YouTubeDataProvider
{
    private string $apiKey;
    private string $regionCode;
    private int $maxResults;

    public function __construct()
    {
        $this->apiKey = config('trends.youtube.api_key');
        $this->regionCode = config('trends.youtube.region_code', 'BR');
        $this->maxResults = config('trends.youtube.max_results', 10);
    }

    /**
     * @return YouTubeTrend[]
     */
    public function fetchTrends(): array
    {
        $response = Http::get('https://www.googleapis.com/youtube/v3/videos', [
            'part' => 'snippet,statistics',
            'chart' => 'mostPopular',
            'regionCode' => $this->regionCode,
            'maxResults' => $this->maxResults,
            'key' => $this->apiKey,
        ]);

        if ($response->failed()) {
            Log::error('Failed to fetch YouTube trends', ['body' => $response->body()]);
            return [];
        }

        $items = $response->json('items', []);
        $trends = [];

        foreach ($items as $item) {
            $snippet = $item['snippet'];
            $statistics = $item['statistics'];

            $trends[] = new YouTubeTrend(
                videoId: $item['id'],
                title: $snippet['title'],
                description: $snippet['description'],
                thumbnailUrl: $snippet['thumbnails']['high']['url'] ?? $snippet['thumbnails']['default']['url'],
                channelTitle: $snippet['channelTitle'],
                viewCount: (int) ($statistics['viewCount'] ?? 0),
                likeCount: (int) ($statistics['likeCount'] ?? 0),
                publishedAt: new \DateTimeImmutable($snippet['publishedAt']),
                tags: $snippet['tags'] ?? []
            );
        }

        return $trends;
    }
}
