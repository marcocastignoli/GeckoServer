<?php

namespace App;

abstract class Model
{
    function __construct()
    {
        Kernel::implementComponents($this, 'Model');
        Kernel::implementComponents($this, 'Database');
    }

    public function get()
    {
        $agents = $this->Database->query('SELECT * FROM ' . static::table);
        return $agents;
    }

    public function create($values)
    {
        if (is_array($values) && count($values) > 0) {
            $columns = array_keys($values);
            $queryColumns = implode(", ", $columns);
            $queryValues = ":" . implode(", :", $columns);
            $query = 'INSERT INTO ' . static::table . ' (' . $queryColumns . ') VALUES (' . $queryValues . ')';
            return $this->Database->query($query, $values);
        }
    }
}