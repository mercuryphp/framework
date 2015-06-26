<?php

namespace System\Web\Routing;

class DynamicRoute extends Route{
    
    protected $route;
    protected $routeDefaults = array();
    
    public function __construct($route, array $routeDefaults = array()){
        $this->route = $route;
        $this->routeDefaults = $routeDefaults;
    }
    
    public function execute(){
        $uri = $this->httpContext->getRequest()->getUri();

        if(preg_match('@'.$this->route.'@', $uri)){
            return new RequestContext($this->httpContext, $this->routeDefaults);
        }
        
        return false;
    }
}

?>