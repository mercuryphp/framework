<?php

namespace System\Web\Routing;

abstract class Route {
    
    protected $httpContext;

    public function setHttpContext(\System\Web\HttpContext $httpContext){
        $this->httpContext = $httpContext;
    }
    
    public abstract function execute();

}

?>