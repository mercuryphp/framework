<?php

namespace System\Web\Routing;

class RouteCollection extends \System\Collections\Collection {

    public function add($routeName, $defaults = array()){
        $route = new Route($routeName, $defaults);
        $this->collection[] = $route;
        return $route;
    }
}