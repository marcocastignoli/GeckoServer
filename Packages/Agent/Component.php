<?php
namespace Agent;

use App;

class Component extends App\Model
{
    const table = "agents";

    public function get()
    {
        $agents = $this->Database->query('SELECT * FROM ' . self::table);
        return $agents;
    }

    public function create($name)
    {
        return $this->Database->query('INSERT INTO ' . self::table . ' (name) VALUES (:name)', [
            'name' => $name
        ]);
    }
}