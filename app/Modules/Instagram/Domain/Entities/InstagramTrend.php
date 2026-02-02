<?php

namespace App\Modules\Instagram\Domain\Entities;

class InstagramTrend
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
