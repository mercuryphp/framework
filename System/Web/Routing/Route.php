<?php

namespace System\Web\Routing;

class Route {
    
    protected $route;
    protected $defaults;
    protected $routeHandler;
    protected $httpRequest;
    
    public function __construct($route, $defaults = array()){
        $this->route = $route;
        $this->defaults = $defaults;
        $this->routeHandler = new RouteHandler();
    }
    
    public function setRouteHandler(RouteHandler $routeHandler){
        $this->routeHandler = $routeHandler;
    }

    public function setHttpRequest(\System\Web\HttpRequest $httpRequest){
        $this->httpRequest = $httpRequest;
    }
    
    public function getRoute(){
        return $this->route;
    }
    
    public function execute(){
        return $this->routeHandler->execute($this->httpRequest, $this->route, $this->defaults);
    }
}