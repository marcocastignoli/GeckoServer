<?php
namespace App;

use App\PDOException;

class Kernel
{
    const PACKAGE_CONTROLLER = 'Controller';
    const PACKAGE_MODEL = 'Model';
    const PACKAGE_CONFIG = 'Config';
    const PACKAGE_COMPONENT = 'Component';

    private static $components = [];
    private static $componentsInstances = [];
    private static $packages = [
        'App'
    ];
    private static $configuration = [];

    private $routes;

    function __construct()
    {
        spl_autoload_register(function ($className) {
            $path = explode('\\', $className);
            $package = $path[0];
            unset($path[0]);
            $file = implode('\\', $path);
            if (!self::includePackageFile($package, $file)) {
                throw new \Exception("Class doesn't exists");
            }
        });
        self::initConfig();
        self::initPackages([
            'JSON',
            'PDO'
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
                $types = [];
                if (property_exists($class, 'types')) {
                    $types = $class::$types;
                }
                if (!in_array($package, $types)) {
                    $types = array_merge($types, [$package]);
                }
                foreach ($types as $componentType) {
                    if (!array_key_exists($componentType, self::$components)) {
                        self::$components[$componentType] = [];
                    }
                    self::$components[$componentType][$package] = $class;
                }
            }
        }
    }

    public static function implementComponents(&$instance, $componentType)
    {
        if (array_key_exists($componentType, self::$components)) {
            foreach (self::$components[$componentType] as $componentName => $componentClass) {
                if (!key_exists($componentClass, static::$componentsInstances)) {
                    try {
                        static::$componentsInstances[$componentClass] = new $componentClass();
                    } catch (\Exception $e) {
                        die(500);
                    }
                }
                $instance->$componentName = static::$componentsInstances[$componentClass];
            }
        }
        return true;
    }

    public static function initConfig()
    {
        $config = [];
        foreach (self::getPackages() as $package) {
            if (self::includePackageFile($package, self::PACKAGE_CONFIG)) {
                self::$configuration = array_merge($config, self::$configuration);
            }
        }
        include self::PACKAGE_CONFIG . '.php';
        self::$configuration = array_merge($config, self::$configuration);
    }

    public static function getConfig($config)
    {
        return self::$configuration[$config];
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
        die(404);
    }
}