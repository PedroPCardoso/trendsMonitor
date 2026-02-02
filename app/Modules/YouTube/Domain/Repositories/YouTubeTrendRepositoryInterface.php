<?php

namespace App\Modules\YouTube\Domain\Repositories;

use App\Modules\YouTube\Domain\Entities\YouTubeTrend;

interface YouTubeTrendRepositoryInterface
{
    /**
     * Save a batch of trends.
     *
     * @param YouTubeTrend[] $trends
     * @return void
     */
    public function saveBatch(array $trends): void;

    /**
     * Get latest trends.
     *
     * @param int $limit
     * @return YouTubeTrend[]
     */
    public function getLatest(int $limit = 10): array;
}
