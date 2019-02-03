<?php
namespace App;

class App extends Application
{
    protected static $app;

    public function __construct($base = '', $paths = [])
    {
        parent::__construct($base, $paths);

        $this->singleton(\Illuminate\Contracts\Http\Kernel::class, \App\Kernel::class);
        $this->singleton(\Illuminate\Contracts\Console\Kernel::class, \App\Console::class);
        $this->singleton(\Illuminate\Contracts\Debug\ExceptionHandler::class, \App\Handler::class);
//        Facade::setFacadeApplication($this);

    }

    public static function App($base = __DIR__ . '/../', $paths = []): App
    {
        $app = static::$app;

        if (!isset(static::$app) || $app === null)
            $app = static::$app = new static($base, $paths);

        $app->singleton(\Illuminate\Contracts\Http\Kernel::class, \App\Kernel::class);
        $app->singleton(\Illuminate\Contracts\Console\Kernel::class, \App\Console::class);
        $app->singleton(\Illuminate\Contracts\Debug\ExceptionHandler::class, \App\Handler::class);

        return $app;
    }
}

//$app = App::App($_ENV['APP_BASE_PATH'] ?? __DIR__ . '/../');;
//
//$app->singleton(Illuminate\Contracts\Http\Kernel::class, \App\Kernel::class);
//$app->singleton(Illuminate\Contracts\Console\Kernel::class, \App\Console::class);
//$app->singleton(Illuminate\Contracts\Debug\ExceptionHandler::class, \App\Handler::class);
//
//return $app;

//return new App($_ENV['APP_BASE_PATH'] ?? __DIR__);
