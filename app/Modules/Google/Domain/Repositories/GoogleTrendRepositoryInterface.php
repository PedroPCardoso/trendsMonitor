<?php

namespace App\Modules\Google\Domain\Repositories;

use App\Modules\Google\Domain\Entities\GoogleTrend;

interface GoogleTrendRepositoryInterface
{
    public function saveBatch(array $trends): void;
    public function getLatest(int $limit = 10): array;
}
