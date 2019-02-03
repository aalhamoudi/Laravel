<?php
namespace App;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Commands\ConfigCacheCommand;

class Console extends ConsoleKernel
{
    protected $commands = [
        ConfigCacheCommand::class
    ];

    public function bootstrap()
    {
        parent::bootstrap();
    }

    /** Define the application's command schedule.*/
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    /** Register the commands for the application */
    protected function commands()
    {
//        $this->load(__DIR__.'/Commands');

        require app_path('Routes/Console.php');
    }
}
