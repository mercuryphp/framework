<?php

namespace System\Std;

class Instance{
    public static function getInstance($name, array $args = array()){
        $name = str_replace(".", "\\", $name);
        $refClass = new \ReflectionClass($name);
        return $refClass->newInstanceArgs($args);
    }
}

