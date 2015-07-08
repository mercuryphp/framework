<?php

namespace System\Web\Routing;

class RouteCollection extends \System\Collections\Collection {

    public function add($route, $defaults = array()){
        $route = new Route($route, $defaults);
        $this->collection[] = $route;
        return $route;
    }
}
