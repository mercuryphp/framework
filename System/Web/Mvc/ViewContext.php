<?php

namespace System\Web\Mvc;

use System\Web\HttpContext;
use System\Collections\Dictionary;

class ViewContext {
    
    protected $httpContext;
    protected $routeData;
    protected $viewBag;
    
    public function __construct(HttpContext $httpContext, Dictionary $routeData, Dictionary $viewBag){
        $this->httpContext = $httpContext;
        $this->routeData = $routeData;
        $this->viewBag = $viewBag;
    }
    
    public function getHttpContext(){
        return $this->httpContext;
    }
    
    public function getRouteData(){
        return $this->routeData;
    }
    
    public function getViewBag(){
        return $this->viewBag;
    }
}

?>
