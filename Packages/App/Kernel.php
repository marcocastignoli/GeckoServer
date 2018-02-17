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

    private static $db;
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
        self::initDatabase();
        self::initPackages([
            'Agent'
        ]);
    }

    public static function initPackages($packages)
    {
        self::$packages = array_merge(self::$packages, $packages);
    }

    public static function initDatabase($database = false)
    {
        if (!$database) {
            self::includePackageFile("App", self::PACKAGE_DATABASE);
            try {
                $database = Database::getInstance();
            } catch (DatabaseException $e) {
                self::getResponse()->reply('Errore nella configurazione del database', Response::CODE_WRONG_DATABASE_CONFIGURATION);
            }
        }
        self::$db = $database;
    }

    public static function initConfig()
    {
        $config = [];
        foreach (self::$packages as $package) {
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

    public static function includePackageFile($package, $file)
    {
        $path = 'Packages/' . $package . '/' . $file . '.php';
        if (in_array($package, self::$packages) && file_exists($path)) {
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