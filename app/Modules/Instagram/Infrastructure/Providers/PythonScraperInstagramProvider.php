<?php

namespace App\Modules\Instagram\Infrastructure\Providers;

use App\Modules\Instagram\Domain\Entities\InstagramTrend;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

class PythonScraperInstagramProvider
{
    private string $scriptPath;
    private array $hashtags;

    public function __construct()
    {
        $this->scriptPath = config('trends.instagram.scraper_path');
        $this->hashtags = config('trends.instagram.hashtags', ['viral']);
    }

    /**
     * @return InstagramTrend[]
     */
    public function fetchTrends(): array
    {
        $tagsString = implode(',', $this->hashtags);
        $command = "python3 {$this->scriptPath} {$tagsString}";
        
        $result = Process::timeout(120)->run($command);

        if ($result->failed()) {
            Log::error('Instagram scraper failed', ['output' => $result->output(), 'error' => $result->errorOutput()]);
            return [];
        }

        $output = $result->output();
        $data = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
             Log::error('Failed to decode Instagram JSON', ['json_error' => json_last_error_msg(), 'output' => $output]);
             return [];
        }

        $trends = [];
        foreach ($data as $item) {
            $trends[] = new InstagramTrend(
                hashtag: $item['hashtag'],
                rank: (int) $item['rank'],
                metadata: $item['metadata'] ?? []
            );
        }

        return $trends;
    }
}
