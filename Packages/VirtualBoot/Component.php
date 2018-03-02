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
        $this->loadPriorities();
        $routes = $this->getRoutes($packages);
        if ($routes) {
            foreach ($routes as $route) {
                App\Kernel::addRoute($route['method'], $route['route'], $route['controller'], $route['action'], $route['middleware']);
            }
        }
    }

    public function scanPriority()
    {
        $priorities = [];
        foreach (App\Kernel::getPackages(true) as $package) {
            if (App\Kernel::includePackageFile($package, App\Kernel::PACKAGE_COMPONENT)) {
                $class = $package . '\\' . App\Kernel::PACKAGE_COMPONENT;
                if (property_exists($class, 'types')) {
                    $types = $class::$types;
                    if (is_array($types)) {
                        foreach ($types as $type) {
                            $priorities[$type][] = $package;
                        }
                    }
                }
            }
        }
        return $priorities;
    }

    public function insertPriority($componentType, $componentName, $priority = false)
    {
        $priorityQuery = '';
        $queryParameters = [];
        if ($priority === false) {
            $priorityQuery = 'SELECT :component_type, :component_name, MAX(priority)+1 FROM priorities WHERE component_type = :component_type_';
            $queryParameters = [
                'component_type' => $componentType,
                'component_name' => $componentName,
                'component_type_' => $componentType
            ];
        } else {
            $priorityQuery = 'VALUES (:component_type, :component_name, :priority)';
            $queryParameters = [
                'component_type' => $componentType,
                'component_name' => $componentName,
                'priority' => $priority
            ];
        }
        return $this->Database->query("INSERT INTO priorities (component_type, component_name, priority) $priorityQuery", $queryParameters);
    }

    private function loadPriorities()
    {
        $priorities = $this->Database->query("
            SELECT 
                component_type,
                component_name,
                priority
            FROM 
                priorities
            ORDER BY
                priority ASC
        ");
        $arr = array();
        foreach ($priorities as $item) {
            $arr[$item['component_type']][$item['priority']] = $item['component_name'];
        }
        foreach ($arr as $componentType => $priorityOrder) {
            App\Kernel::setPriority($componentType, $priorityOrder);
        }
    }

    public function getRoutes($packages)
    {
        if (is_array($packages) && count($packages) > 0) {
            $qMarks = str_repeat('?,', count($packages) - 1) . '?';
            return $this->Database->query("
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
    public function installRoutes($package)
    {
        if (App\Kernel::includePackageFile($package, App\Kernel::PACKAGE_ROUTES, true)) {
            $class = $package . '\\' . App\Kernel::PACKAGE_ROUTES;
            $group = 'default';
            if (method_exists($class, $group)) {
                $routesGroup = $class::$group(false);
                if (is_array($routesGroup)) {
                    foreach ($routesGroup as $route) {
                        if (!isset($route[App\Kernel::ROUTES_MIDDLEWARE])) {
                            $route[App\Kernel::ROUTES_MIDDLEWARE] = null;
                        }
                        $this->Database->query("INSERT INTO routes (package_id, method, route, controller, action, middleware) VALUES ((SELECT package_id FROM packages WHERE name = :package), :method, :route, :controller, :action, :middleware)", [
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
        return $this->Database->query("INSERT INTO packages (name) VALUES (:name)", $values);
    }
    private function delete($values)
    {
        return $this->Database->query("DELETE FROM packages WHERE name = (:name)", $values);
    }
    private function isActive($package)
    {
        $res = $this->Database->query("SELECT active FROM packages WHERE name = :name", [
            'name' => $package
        ]);
        if (count($res) > 0) {
            return $res[0]['active'] === "1" ? true : false;
        }
    }
    private function setActive($package, $active, $force = false)
    {
        if ($force || $this->isActive($package) !== $active) {
            return $this->Database->query("UPDATE packages SET active = :active WHERE name = (:name)", [
                'active' => $active,
                'name' => $package
            ]);
        }
        throw new \Exception("Package already in the same state");
    }
    public function getActivePackages()
    {
        $res = $this->Database->query("SELECT name FROM packages WHERE active = 1");
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
                $instance = new $class();
                $instance->$action();
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
        $this->deactivate($package, true);
        $this->delete([
            'name' => $package
        ]);
        $this->callGeckoAction($package, 'uninstall');
    }
    public function activate($package, $force = false)
    {
        $this->setActive($package, true, $force);
        $this->callGeckoAction($package, 'activate');
    }
    public function deactivate($package, $force = false)
    {
        $this->setActive($package, false, $force);
        $this->callGeckoAction($package, 'deactivate');
    }
}