<?php

namespace App\Http\Controllers;

use App\Modules\Google\Domain\Repositories\GoogleTrendRepositoryInterface;
use App\Modules\Instagram\Domain\Repositories\InstagramTrendRepositoryInterface;
use App\Modules\TikTok\Domain\Repositories\TikTokTrendRepositoryInterface;
use App\Modules\YouTube\Domain\Repositories\YouTubeTrendRepositoryInterface;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly YouTubeTrendRepositoryInterface $youtubeRepo,
        private readonly GoogleTrendRepositoryInterface $googleRepo,
        private readonly TikTokTrendRepositoryInterface $tiktokRepo,
        private readonly InstagramTrendRepositoryInterface $instagramRepo
    ) {}

    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'trends' => [
                'youtube' => $this->youtubeRepo->getLatest(),
                'google' => $this->googleRepo->getLatest(),
                'tiktok' => $this->tiktokRepo->getLatest(),
                'instagram' => $this->instagramRepo->getLatest(),
            ],
            'last_update' => now()->toDateTimeString(), // Placeholder, ideally fetch metadata
        ]);
    }
}
