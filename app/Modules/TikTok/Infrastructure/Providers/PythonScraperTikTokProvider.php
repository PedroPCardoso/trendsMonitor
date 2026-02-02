<?php

namespace App\Modules\TikTok\Infrastructure\Providers;

use App\Modules\TikTok\Domain\Entities\TikTokTrend;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

class PythonScraperTikTokProvider
{
    private string $scriptPath;
    private string $region;

    public function __construct()
    {
        $this->scriptPath = config('trends.tiktok.scraper_path');
        $this->region = 'BR'; // Could be config
    }

    /**
     * @return TikTokTrend[]
     */
    public function fetchTrends(): array
    {
        $command = "python3 {$this->scriptPath} {$this->region}";
        // Playwright might take time
        $result = Process::timeout(120)->run($command);

        if ($result->failed()) {
            Log::error('TikTok scraper failed', ['output' => $result->output(), 'error' => $result->errorOutput()]);
            return [];
        }

        $output = $result->output();
        $data = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
             Log::error('Failed to decode TikTok JSON', ['json_error' => json_last_error_msg(), 'output' => $output]);
             return [];
        }

        $trends = [];
        foreach ($data as $item) {
            $trends[] = new TikTokTrend(
                hashtag: $item['hashtag'],
                rank: (int) $item['rank'],
                metadata: $item['metadata'] ?? []
            );
        }

        return $trends;
    }
}
