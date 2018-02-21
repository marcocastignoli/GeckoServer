<?php

namespace App;

class ComponentInterface
{
    protected $components;

    public function addComponent($name, $instance)
    {
        $this->components[$name] = $instance;
    }

    public function use($name = false)
    {
        $components = $this->components;
        $component = null;
        if ($name) {
            $component = $components[$name];
        } else {
            $component = reset($components);
        }
        return $component;
    }

    public function __call($methodName, $arguments)
    {
        $component = $this->use();
        return call_user_func_array([$component, $methodName], $arguments);
    }
}