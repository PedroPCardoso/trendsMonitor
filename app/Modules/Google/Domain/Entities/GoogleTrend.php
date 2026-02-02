<?php

namespace App\Modules\Google\Domain\Entities;

class GoogleTrend
{
    public function __construct(
        public readonly string $title,
        public readonly array $entityNames,
        public readonly int $rank,
        public readonly \DateTimeImmutable $fetchedAt
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'entity_names' => $this->entityNames,
            'rank' => $this->rank,
            'fetched_at' => $this->fetchedAt->format('Y-m-d H:i:s'),
        ];
    }
}
