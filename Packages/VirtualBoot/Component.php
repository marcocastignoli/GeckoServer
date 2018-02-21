<?php
namespace VirtualBoot;

use App;

class Component extends App\Model
{
    public static $types = ['Kernel'];

    public const PACKAGE_GECKO = 'Gecko';


    public function boot()
    {
        $packages = $this->getActivePackages();
        App\Kernel::initPackages($packages);
        App\Kernel::initComponents();
        $routes = $this->getRoutes($packages);
        foreach ($routes as $route) {
            App\Kernel::addRoute($route['method'], $route['route'], $route['controller'], $route['action'], $route['middleware']);
        }
    }
    public function getRoutes($packages)
    {
        if (is_array($packages) && count($packages) > 0) {
            $qMarks = str_repeat('?,', count($packages) - 1) . '?';
            return $this->PDO->query("
            SELECT 
                method,
                route,
                controller,
                action,
                middleware
            FROM 
                routes AS r
            LEFT JOIN 
                packages AS p ON p.package_id = r.package_id
            WHERE
                p.name IN ($qMarks)
            ", $packages);
        }
    }
    public function installRoutes($package){
        if (App\Kernel::includePackageFile($package, App\Kernel::PACKAGE_ROUTES, true)) {
            $class = $package . '\\' . App\Kernel::PACKAGE_ROUTES;
            $group = 'default';
            if (method_exists($class, $group)) {
                $routesGroup = $class::$group(false);
                if(is_array($routesGroup)){
                    foreach($routesGroup as $route){
                        if(!isset($route[App\Kernel::ROUTES_MIDDLEWARE])){
                            $route[App\Kernel::ROUTES_MIDDLEWARE] = null;
                        }
                        $this->PDO->query("INSERT INTO routes (package_id, method, route, controller, action, middleware) VALUES ((SELECT package_id FROM packages WHERE name = :package), :method, :route, :controller, :action, :middleware)", [
                            "package" => $package,
                            "method" => $route[App\Kernel::ROUTES_METHOD],
                            "route" => $route[App\Kernel::ROUTES_ROUTE],
                            "controller" => $route[App\Kernel::ROUTES_CONTROLLER],
                            "action" => $route[App\Kernel::ROUTES_ACTION],
                            "middleware" => $route[App\Kernel::ROUTES_MIDDLEWARE],
                        ]);
                    }
                    return true;
                }
            }
        }
    }
    private function insert($values)
    {
        return $this->PDO->query("INSERT INTO packages (name) VALUES (:name)", $values);
    }
    private function delete($values)
    {
        return $this->PDO->query("DELETE FROM packages WHERE name = (:name)", $values);
    }
    private function isActive($package)
    {
        $res = $this->PDO->query("SELECT active FROM packages WHERE name = :name", [
            'name' => $package
        ]);
        if (count($res) > 0) {
            return $res[0]['active'] === "1" ? true : false;
        }
    }
    private function setActive($package, $active)
    {
        if ($this->isActive($package) !== $active) {
            return $this->PDO->query("UPDATE packages SET active = :active WHERE name = (:name)", [
                'active' => $active,
                'name' => $package
            ]);
        }
        throw new \Exception("Package already in the same state");
    }
    public function getActivePackages()
    {
        $res = $this->PDO->query("SELECT name FROM packages WHERE active = 1");
        if (count($res) > 0) {
            return array_column($res, 'name');
        }
        return [];
    }
    public function callGeckoAction($package, $action)
    {
        if (App\Kernel::includePackageFile($package, self::PACKAGE_GECKO, true)) {
            $class = $package . '\\' . self::PACKAGE_GECKO;
            if (method_exists($class, $action)) {
                $class::$action();
            }
            return true;
        }
    }
    public function install($package)
    {
        if (App\Kernel::packageExists($package)) {
            $this->insert([
                'name' => $package
            ]);
            $this->installRoutes($package);
            $this->callGeckoAction($package, 'install');
        }
    }
    public function uninstall($package)
    {
        $this->deactivate($package);
        $this->delete([
            'name' => $package
        ]);
        $this->callGeckoAction($package, 'uninstall');
    }
    public function activate($package)
    {
        $this->setActive($package, true);
        $this->callGeckoAction($package, 'activate');
    }
    public function deactivate($package)
    {
        $this->setActive($package, false);
        $this->callGeckoAction($package, 'deactivate');
    }
}