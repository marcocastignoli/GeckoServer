<?php
namespace Agent;

use App;

App\Kernel::includePackageFile("App", App\Kernel::PACKAGE_MODEL);

class Model extends App\Model
{
    const table = "agents";

    public function get()
    {
        $data = $this->db->query('SELECT * FROM ' . self::table);
        $agents = new App\ModelCollection($this, $data);
        return $agents;
    }

    public function create($name)
    {
        return $this->db->query('INSERT INTO ' . self::table . ' (name) VALUES (:name)', [
            'name' => $name
        ]);
    }
}