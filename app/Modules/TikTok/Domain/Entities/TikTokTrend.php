<?php

namespace App\Modules\TikTok\Domain\Entities;

class TikTokTrend
{
    public function __construct(
        public readonly string $hashtag,
        public readonly int $rank,
        public readonly ?array $metadata = []
    ) {}

    public function toArray(): array
    {
        return [
            'hashtag' => $this->hashtag,
            'rank' => $this->rank,
            'metadata' => $this->metadata,
        ];
    }
}
