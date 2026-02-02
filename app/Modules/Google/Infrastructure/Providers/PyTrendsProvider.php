<?php

namespace App\Modules\Google\Infrastructure\Providers;

use App\Modules\Google\Domain\Entities\GoogleTrend;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

class PyTrendsProvider
{
    private string $scriptPath;
    private string $region;

    public function __construct()
    {
        $this->scriptPath = base_path('python/google_trends.py');
        $this->region = config('trends.google.region', 'BR');
    }

    /**
     * @return GoogleTrend[]
     */
    public function fetchTrends(): array
    {
        $command = "python3 {$this->scriptPath} {$this->region}";
        $result = Process::run($command);

        if ($result->failed()) {
            Log::error('PyTrends script failed', ['output' => $result->output(), 'error' => $result->errorOutput()]);
            return [];
        }

        $output = $result->output();
        if (empty($output)) {
             return [];
        }

        $data = json_decode($output, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Failed to decode PyTrends JSON', ['json_error' => json_last_error_msg(), 'output' => $output]);
            return [];
        }

        $trends = [];
        $now = new \DateTimeImmutable();

        foreach ($data as $item) {
            $trends[] = new GoogleTrend(
                title: $item['title'],
                entityNames: $item['entity_names'] ?? [],
                rank: (int) $item['rank'],
                fetchedAt: $now
            );
        }

        return $trends;
    }
}
