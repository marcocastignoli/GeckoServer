<?php

namespace App;

abstract class Model
{
    protected $properties;

    function __construct($properties = false)
    {
        Kernel::implementComponents($this, 'Model');
        Kernel::implementComponents($this, 'Database');
        $this->properties = $properties;
    }

    function getProperties($property = false)
    {
        if ($property && array_key_exists($property, $this->properties)) {
            return $this->properties[$property];
        } else if (count($this->properties) > 0) {
            return $this->properties;
        }
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