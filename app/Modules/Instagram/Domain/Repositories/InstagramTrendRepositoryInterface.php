<?php

namespace App\Modules\Instagram\Domain\Repositories;

use App\Modules\Instagram\Domain\Entities\InstagramTrend;

interface InstagramTrendRepositoryInterface
{
    public function saveBatch(array $trends): void;
    public function getLatest(int $limit = 10): array;
}
