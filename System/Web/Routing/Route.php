<?php

namespace System\Web\Routing;

class Route {
    
    protected $namespace;
    protected $route;
    protected $defaults;
    protected $constraints;
    protected $routeHandler;
    protected $httpRequest;
    
    public function __construct($route, $defaults = array(), $constraints = array()){
        $this->route = $route;
        $this->defaults = $defaults;
        $this->constraints = $constraints;
        $this->routeHandler = new RouteHandler();
    }
    
    public function setRouteHandler(IRouteHandler $routeHandler){
        $this->routeHandler = $routeHandler;
        return $this;
    }

    public function setHttpRequest(\System\Web\HttpRequest $httpRequest){
        $this->httpRequest = $httpRequest;
        return $this;
    }
    
    public function getRoute(){
        return $this->route;
    }
    
    public function setNamespace($namespace){
        $this->namespace = $namespace;
        return $this;
    }
    
    public function getNamespace(){
        return $this->namespace;
    }

    public function execute(){
        return $this->routeHandler->execute($this->httpRequest, $this->route, $this->defaults, $this->constraints);
    }
}