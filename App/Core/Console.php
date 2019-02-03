<?php
namespace App;

use Illuminate\Console\Scheduling\Schedule;

use App\Commands\ConfigCacheCommand;


if ($mode = 'Laravel')
    class_alias(\Illuminate\Foundation\Console\Kernel::class, ConsoleKernel::class);
else
    class_alias(\Laravel\Lumen\Console\Kernel::class, ConsoleKernel::class);


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
