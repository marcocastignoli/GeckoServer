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
        foreach ($packages as $package) {
            App\Kernel::loadPackageRoutes($package);
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
        if (App\Kernel::includePackageFile($package, self::PACKAGE_GECKO)) {
            $class = $package . '\\' . self::PACKAGE_GECKO;
            if (method_exists($class, $action)) {
                $class::$action();
            }
            return true;
        }
        throw new \Exception("Package doesn't exists");
    }
    public function install($package)
    {
        $this->insert([
            'name' => $package
        ]);
        $this->callGeckoAction($package, 'install');
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