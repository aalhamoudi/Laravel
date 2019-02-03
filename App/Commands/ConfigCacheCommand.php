<?php
namespace App\Commands;

use App\App;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Console\ConfigCacheCommand as ConfigCache;

class ConfigCacheCommand extends ConfigCache
{
    public function getFreshConfiguration()
    {
        /** @var App $app */
        $app = App::App();
        $app->make(Kernel::class);

        return $app['config']->all();
    }
}
