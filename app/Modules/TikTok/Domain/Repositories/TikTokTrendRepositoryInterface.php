<?php

namespace App\Modules\TikTok\Domain\Repositories;

use App\Modules\TikTok\Domain\Entities\TikTokTrend;

interface TikTokTrendRepositoryInterface
{
    public function saveBatch(array $trends): void;
    public function getLatest(int $limit = 10): array;
}
