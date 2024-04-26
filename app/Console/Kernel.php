<?php

namespace App\Console;

use App\Jobs\TaskCronJob;
use App\Jobs\WorkItemJob;
use App\Services\Private\WorkService;
use App\Services\Private\FileService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            WorkItemJob::dispatch();
        })->everyMinute()->name("员工考勤任务超时监听");

        $schedule->call(function () {
            TaskCronJob::dispatch();
        })->everyThirtyMinutes()->name("任务配置定时任务");

        $schedule->call(function () {
            (new WorkService)->makePreDay();
        })->everyFourHours()->name("检查每日员工考勤基础");
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
