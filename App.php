<?php
/** @noinspection ALL */

use App\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel;

use \Illuminate\Support\Facades\Facade as Facade;

class Application extends \Illuminate\Foundation\Application
{
    protected $namespace;

    protected $basePath;
    protected $environmentPath;
    protected $configPath;
    protected $appPath;

    protected $bootstrapPath;
    protected $databasePath;
    protected $storagePath;
    protected $cachePath;

    protected $resourcePath;
    protected $publicPath;
    protected $langPath;


    // Dirs
    protected $baseDir = '/';
    protected $appDir = '/App';
    protected $configDir = '/App/Config';
    protected $environmentDir = '/App/Config';

    protected $bootstrapDir = '/Transient';
    protected $storageDir = '/Transient/Storage';
    protected $cacheDir = '/Transient/Cache';
    protected $databaseDir = '/Transient/Database';


    protected $resourceDir = '/Resources';
    protected $publicDir = '/Resources/Public';
    protected $langDir = '/Resources/Languages';


    public function __construct($base = '', $paths = [])
    {
        parent::__construct($base);
        $paths = [
            'app' => '/App',
            'config' => '/App/Config',
            'environment' => '/App/Config',
            'resource' => '/Resources',
            'public' => '/Resources/Public',
            'lang' => '/Resources/Languages',
            'bootstrap' => '/Transient',
            'storage' => '/Transient/Storage',
            'cache' => '/Transient/Cache',
            'database' => '/Transient/Database'
        ];

        foreach ($paths as $name => $path)
            $this->SetPath($name, $path);

        $this->bind('path.public', function () {return base_path() . '/Public'; });
    }

    public function SetPath($target, $path = ''): Application
    {
        $path = $path?? $this->basePath . ($target ?? $this[$target . 'Dir']);
        $this[$target . 'Path'] = $path;

        if ($target !== 'environment')
            $this->instance('path.' . $target, $path);

        return $this;
    }

    // App
    public function basePath($path = '')
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function bootstrapPath($path = '')
    {
        return ($this->bootstrapPath ?? $this->basePath . $this->bootstrapDir) . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function configPath($path = '')
    {
        return ($this->configPath ?? $this->basePath . DIRECTORY_SEPARATOR . '/App/Config') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    public function environmentPath()
    {
        return $this->environmentPath ?: $this->basePath . '/App/Config';
    }


    // Resources
    public function publicPath()
    {
        return $this->publicPath ?? $this->basePath . DIRECTORY_SEPARATOR . 'Public';
    }

    public function resourcePath($path = '')
    {
        $resourcePath = $this->resourcePath ? $this->resourcePath : $this->basePath . DIRECTORY_SEPARATOR . 'Resources';
        return $resourcePath . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function langPath()
    {
        echo $this->resourcePath;
        return $this->langPath ?? $this->resourcePath() . DIRECTORY_SEPARATOR . 'Languages';
    }


    // Cache
    public function getCachedServicesPath()
    {
        return $this->cachePath ? $this->cachePath . '/Services.php' : $this->bootstrapPath() . '/Cache/Services.php';
    }

    public function getCachedPackagesPath()
    {
        return $this->cachePath ? $this->cachePath . '/Packages.php' : $this->bootstrapPath() . '/Cache/Packages.php';
    }

    public function getCachedConfigPath()
    {
        return $_ENV['APP_CONFIG_CACHE'] ?? ($this->cachePath ? $this->cachePath . '/Config.php' : $this->bootstrapPath() . '/Cache/Config.php');
    }

    public function getCachedRoutesPath()
    {
        return $this->cachePath ? $this->cachePath . '/Routes.php' : $this->bootstrapPath() . '/Cache/Routes.php';
    }


    // Namespace
    public function getNamespace()
    {
        if ($this->namespace !== null)
            return $this->namespace;

        $composer = json_decode(file_get_contents(base_path('composer.json')), true);
        foreach ((array)data_get($composer, 'autoload.psr-4') as $namespace => $path) {
            foreach ((array)$path as $pathChoice) {
                if (realpath(app_path()) === realpath(base_path() . '/' . $pathChoice))
                    return $this->namespace = $namespace;

            }
        }

        throw new RuntimeException('Unable to detect application namespace.');
    }
}

class App extends Application
{
    public function __construct($base = '', $paths = [])
    {
        parent::__construct($base, $paths);
        $this->singleton(Illuminate\Contracts\Http\Kernel::class, App\Kernel::class);
        $this->singleton(Illuminate\Contracts\Console\Kernel::class, App\Console::class);
        $this->singleton(Illuminate\Contracts\Debug\ExceptionHandler::class, App\Handler::class);
        Facade::setFacadeApplication($this);
    }
}

return new App($_ENV['APP_BASE_PATH'] ?? __DIR__);
