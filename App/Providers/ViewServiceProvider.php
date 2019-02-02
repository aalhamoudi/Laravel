<?php
namespace App\Providers;

use Illuminate\View\ViewServiceProvider as ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function boot()
    {
        
    }
}
