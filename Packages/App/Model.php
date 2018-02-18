<?php

namespace App;

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