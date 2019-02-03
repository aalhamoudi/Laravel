<?php
namespace App;

use Illuminate\Foundation\Application as Laravel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\FileSessionHandler;

class Application extends Laravel
{
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

        'resource' => '/Resources',
        'public' => '/Resources/Public',
        'lang' => '/Resources/Languages',

        'bootstrap' => '/Transient',
        'storage' => '/Transient/Storage',
        'cache' => '/Transient/Cache',
        'database' => '/Transient/Database'
    ];

    public function __construct($base = '', $paths = [])
    {
        parent::__construct($base);

        if (count($paths))
            $paths = $this->paths = array_merge($this->paths, $paths);

        foreach ($paths as $name => $path)
            $this->SetPath($name);

//        $this->bind('path.public', function () {return base_path() . '/Public'; });
    }

    public function SetPath($target, $path = ''): Application
    {
        $path = $path?: $this->basePath . ($target ?: $this->paths[$target . 'Dir']);
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
        return ($this->bootstrapPath ?: $this->basePath . $this->bootstrapDir) . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function configPath($path = '')
    {
        return ($this->configPath ?: $this->basePath . DIRECTORY_SEPARATOR . '/App/Config') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    public function environmentPath()
    {
        return $this->environmentPath ?: $this->basePath . '/App/Config';
    }

    public function storagePath()
    {
        return $this->storagePath ?: $this->bootstrapPath() . DIRECTORY_SEPARATOR . 'Storage';
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
