<?php

namespace System\Web\Routing;

final class RequestContext {
    
    private $httpContext;
    private $routeData;

    public function __construct(\System\Web\HttpContext $httpContext, array $routeData){
        $this->httpContext = $httpContext;
        $this->routeData = new \System\Collections\Dictionary($routeData);
    }
    
    public function getHttpContext(){
        return $this->httpContext;
    }
    
    public function getRouteData(){
        return $this->routeData;
    }
}

?>
