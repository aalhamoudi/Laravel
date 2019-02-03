<?php
namespace App;

try { (new \Dotenv\Dotenv(__DIR__ . '/../Config'))->load(); }
catch (\Dotenv\Exception\InvalidPathException $e) {}

$mode = env('APP_MODE', 'Lumen');

if ($mode === 'Laravel')
    class_alias(\Illuminate\Foundation\Application::class, Laravel::class);
else
    class_alias(\Laravel\Lumen\Application::class, Laravel::class);

function Merge(&$inner, &$outer)
{
    if (!count($outer))
        return $inner;
    else
        return $inner = $outer = array_merge($inner, $outer);
}



class Application extends Laravel
{
    protected static $app;

    protected $namespace;

    protected $basePath;

    protected $appPath;
    protected $configPath;
    protected $environmentPath;

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

    protected $paths = [
        'app' => '/App',
        'config' => '/App/Config',
        'environment' => '/App/Config',

        'public' => '/App/Public',
        'resource' => '/App/Resources',
        'lang' => '/App/Resources/Languages',

        'bootstrap' => '/Transient',
        'storage' => '/Transient/Storage',
        'cache' => '/Transient/Cache',
        'database' => '/Transient/Database'
    ];

    protected $singletons = [
        \Illuminate\Contracts\Http\Kernel::class => \App\Core\Kernel::class,
        \Illuminate\Contracts\Debug\ExceptionHandler::class => \App\Exceptions\Handler::class,
        \Illuminate\Contracts\Console\Kernel::class => Console::class
    ];
    protected $providers = [
        \App\Providers\AppServiceProvider::class,
        \App\Providers\AuthServiceProvider::class,
        \App\Providers\EventServiceProvider::class
    ];
    protected $middlewares = [];
    protected $routeMiddlewares = [
        ['auth' => \App\Middleware\Authenticate::class]
    ];

    public function __construct($base = '', $paths = [], $singletons = [], $providers = [], $middlewares = [])
    {
        parent::__construct($base);
        global $mode;

        Merge($this->paths, $paths);
        Merge($this->singletons, $singletons);
        Merge($this->providers, $providers);
        Merge($this->middlewares, $middlewares);

        foreach ($paths as $name => $path)
            $this->SetPath($name);


        if ($mode === 'Lumen') {
            $this->withFacades();
            $this->withEloquent();
            foreach ($this->routeMiddlewares as $routeMiddleware)
                $this->routeMiddleware($routeMiddleware);
            $this->router->group(['namespace' => 'App\Controllers'], function($router) {require __DIR__.'/../Routes/Lumen.php'; });
        }
    }

    public function SetPath($target, $path = ''): Application
    {
        $path = $path?: $this->basePath . ($target ?: $this->paths[$target . 'Dir']);
        $this[$target . 'Path'] = $path;

        if ($target !== 'environment')
            $this->instance('path.' . $target, $path);

        return $this;
    }

    public function Init()
    {
        $this->Singletons();
        $this->Providers();
        $this->Middlewares();

        return $this;
    }

    public function Singletons()
    {
        foreach ($this->singletons as $abstract => $concrete)
            $this->singleton($abstract, $concrete);
    }

    public function Providers()
    {
        foreach ($this->providers as $provider)
            $this->register($provider);
    }

    public function Middlewares()
    {
        foreach ($this->middlewares as $middleware)
            $this->middleware($middleware);
    }


    // App
    public function basePath($path = '')
    {
        if (isset($this->basePath))
            return $this->basePath.($path ? '/'.$path : $path);


        if ($this->runningInConsole())
            $this->basePath = getcwd();
        else
            $this->basePath = realpath(getcwd().'/../');

        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function bootstrapPath($path = '')
    {
        return ($this->bootstrapPath ?: $this->basePath . $this->bootstrapDir) . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function configPath($name = '')
    {
        $configPath = $this->configPath ?: $this->basePath . DIRECTORY_SEPARATOR . '/App/Config' ;
        $resolvedPath = $configPath . ($name ? DIRECTORY_SEPARATOR . $name : $name);

        return $resolvedPath;
    }

    public function getConfigurationPath($name = null)
    {
        return $this->configPath($name);
    }

    public function environmentPath()
    {
        return $this->environmentPath ?: $this->basePath . '/App/Config';
    }

    public function storagePath()
    {
        return $this->storagePath ?: $this->bootstrapPath() . DIRECTORY_SEPARATOR . 'Storage';
    }

    public function databasePath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'database'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    // Resources
    public function publicPath()
    {
        return $this->publicPath ?: $this->basePath . DIRECTORY_SEPARATOR . 'Public';
    }

    public function resourcePath($path = '')
    {
        return $this->resourcePath ?: $this->basePath . DIRECTORY_SEPARATOR . 'Resources' . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function langPath()
    {
        echo $this->resourcePath;
        return $this->langPath ?: $this->resourcePath() . DIRECTORY_SEPARATOR . 'Languages';
    }

    protected function getLanguagePath()
    {
        if (is_dir($langPath = $this->basePath().'/resources/lang'))
            return $langPath;
        else
            return __DIR__.'/../resources/lang';

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

    public static function App($base = __DIR__ . '/../../', $paths = [], $singletons = [], $providers = [], $middlewares = [])
    {
        $app = static::$app;
        if (!isset($app) || $app === null)
            $app = static::$app = new static($base, $paths, $singletons, $providers, $middlewares);

        return $app->Init();
    }
}
