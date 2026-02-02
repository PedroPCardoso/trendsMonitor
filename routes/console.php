use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new \App\Modules\YouTube\Infrastructure\Jobs\FetchYouTubeTrendsJob)->hourly();
Schedule::job(new \App\Modules\Google\Infrastructure\Jobs\FetchGoogleTrendsJob)->hourly();
Schedule::job(new \App\Modules\TikTok\Infrastructure\Jobs\FetchTikTokTrendsJob)->hourly();
Schedule::job(new \App\Modules\Instagram\Infrastructure\Jobs\FetchInstagramTrendsJob)->hourly();

