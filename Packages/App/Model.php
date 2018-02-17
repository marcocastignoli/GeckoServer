<?php

namespace App;

class ModelCollection
{
    protected $collection;

    function __construct($model, $data)
    {
        $this->collection = [];
        foreach ($data as $properties) {
            $this->collection[] = new $model($properties);
        }
        return $this;
    }

    public function getProperties()
    {
        $properties = [];
        foreach ($this->collection as $model) {
            $properties[] = $model->getProperties();
        }
        return $properties;
    }
}

abstract class Model
{
    protected $properties;

    function __construct($properties = false)
    {
        Kernel::implementComponents($this, 'Model');
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
}