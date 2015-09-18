<?php

namespace System\Web\Routing;

class RouteCollection extends \System\Collections\Collection {

    public function add($routeName, $defaults = array(), $constraints = array()){
        $route = new Route($routeName, $defaults, $constraints);
        $this->collection[] = $route;
        return $route;
    }
}