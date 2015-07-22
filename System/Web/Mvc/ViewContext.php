<?php

namespace System\Web\Mvc;

use System\Web\HttpContext;
use System\Collections\Dictionary;

class ViewContext {
    
    protected $httpContext;
    protected $viewBag;
    
    public function __construct(HttpContext $httpContext, Dictionary $viewBag, $viewName = null){
        $this->httpContext = $httpContext;
        $this->viewBag = $viewBag;
        
        if($viewName){
            $this->httpContext->getRequest()->getRouteData()->set('action', $viewName);
        }
    }
    
    public function getHttpContext(){
        return $this->httpContext;
    }
    
    public function getRouteData(){
        return  $this->httpContext->getRequest()->getRouteData();
    }
    
    public function getViewBag(){
        return $this->viewBag;
    }
}

?>
