<?php
namespace App;

use App\DatabaseException;

class Kernel
{
    const PACKAGE_CONTROLLER = 'Controller';
    const PACKAGE_MODEL = 'Model';
    const PACKAGE_DATABASE = 'Database';
    const PACKAGE_CONFIG = 'Config';
    const PACKAGE_RESPONSE = 'Response';
    const PACKAGE_COMPONENT = 'Component';

    private static $components = [];
    private static $response;
    private static $packages = [
        'App'
    ];
    private static $configuration = [];

    private $routes;

    function __construct()
    {
        self::initResponse();
        self::initConfig();
        //self::initDatabase();
        self::initPackages([
            'Agent',
            'Database'
        ]);
        self::initComponents();
    }

    public static function initPackages($packages)
    {
        self::$packages = array_merge(self::$packages, $packages);
    }

    public static function initComponents()
    {
        foreach (self::getPackages(true) as $package) {
            if (self::includePackageFile($package, self::PACKAGE_COMPONENT)) {
                $class = $package . '\\' . self::PACKAGE_COMPONENT;
                if (is_array($class::$types) && method_exists($class, 'getInstance')) {
                    foreach ($class::$types as $componentType) {
                        if (!array_key_exists($componentType, self::$components)) {
                            self::$components[$componentType] = [];
                        }
                        try {
                            self::$components[$componentType][$package] = $class::getInstance();
                        } catch (\Exception $e) {
                            self::getResponse()->reply($e->getMessage(), $e->getCode());
                        }
                        
                    }
                }
            }
        }
    }

    public static function implementComponents(&$instance, $componentType)
    {
        foreach (self::$components[$componentType] as $componentName => $componentInstance) {
            $instance->$componentName = $componentInstance;
        }
        return true;
    }

    public static function initConfig()
    {
        $config = [];
        foreach (self::getPackages() as $package) {
            self::includePackageFile($package, self::PACKAGE_CONFIG);
            self::$configuration = array_merge($config, self::$configuration);
        }
        include self::PACKAGE_CONFIG . '.php';
        self::$configuration = array_merge($config, self::$configuration);
    }

    public static function initResponse($response = false)
    {
        if (!$response) {
            self::includePackageFile("App", self::PACKAGE_RESPONSE);
            $response = Response::getInstance();
        }
        self::$response = $response;
    }

    public static function getConfig($config)
    {
        return self::$configuration[$config];
    }

    public static function getDatabase()
    {
        return self::$db;
    }

    public static function getResponse()
    {
        return self::$response;
    }

    public static function getPackages($excludeAbstracts = false)
    {
        $packages = self::$packages;
        if ($excludeAbstracts) {
            $packages = array_diff(self::$packages, ['App']);
        }
        return $packages;
    }

    public static function includePackageFile($package, $file)
    {
        $path = 'Packages/' . $package . '/' . $file . '.php';
        if (in_array($package, self::getPackages()) && file_exists($path)) {
            require_once $path;
            return $path;
        }
        return false;
    }

    public function addRoute($method, $route, $controller, $action, $middleware = false)
    {
        $this->routes[$method][$route] = [$controller, $action, $middleware];
    }

    public function serve()
    {
        if (array_key_exists($_SERVER['REQUEST_METHOD'], $this->routes)) {
            if (array_key_exists(@$_REQUEST['route'], $this->routes[$_SERVER['REQUEST_METHOD']])) {
                list($package, $action, $middleware) = $this->routes[$_SERVER['REQUEST_METHOD']][$_REQUEST['route']];
                if ($this->includePackageFile($package, self::PACKAGE_CONTROLLER)) {
                    $class = $package . '\\' . self::PACKAGE_CONTROLLER;
                    if (method_exists($class, $action)) {
                        $controllerInstance = new $class();
                        $arguments = $_REQUEST;
                        $next = true;
                        if (is_callable($middleware)) {
                            $next = $middleware($arguments);
                        }
                        if ($next) {
                            return $controllerInstance->$action($arguments);
                        } else {
                            return false;
                        }
                    }
                }
            }
        }
        self::getResponse()->reply("Questo percorso non esiste", Response::CODE_MISSING_ROUTE);
    }
}