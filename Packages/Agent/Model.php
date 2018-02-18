<?php
namespace Agent;

use App;

App\Kernel::includePackageFile("App", App\Kernel::PACKAGE_MODEL);

class Model extends App\Model
{
    const table = "agents";

    public function get()
    {
        $agents = $this->PDO->query('SELECT * FROM ' . self::table);
        return $agents;
    }

    public function create($name)
    {
        return $this->PDO->query('INSERT INTO ' . self::table . ' (name) VALUES (:name)', [
            'name' => $name
        ]);
    }
}