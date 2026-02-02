<?php

namespace App\Modules\YouTube\Domain\Entities;

class YouTubeTrend
{
    public function __construct(
        public readonly string $videoId,
        public readonly string $title,
        public readonly string $description,
        public readonly string $thumbnailUrl,
        public readonly string $channelTitle,
        public readonly int $viewCount,
        public readonly int $likeCount,
        public readonly \DateTimeImmutable $publishedAt,
        public readonly ?array $tags = []
    ) {}

    public function toArray(): array
    {
        return [
            'video_id' => $this->videoId,
            'title' => $this->title,
            'description' => $this->description,
            'thumbnail_url' => $this->thumbnailUrl,
            'channel_title' => $this->channelTitle,
            'view_count' => $this->viewCount,
            'like_count' => $this->likeCount,
            'published_at' => $this->publishedAt->format('Y-m-d H:i:s'),
            'tags' => $this->tags,
        ];
    }
}
