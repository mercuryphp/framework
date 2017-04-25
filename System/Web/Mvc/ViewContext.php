<?php

namespace System\Web\Mvc;

use System\Web\HttpContext;
use System\Collections\Dictionary;

class ViewContext {
    
    protected $httpContext;
    protected $viewBag;
    
    public function __construct(HttpContext $httpContext, Dictionary $viewBag, $arg = null){
        $this->httpContext = $httpContext;
        $this->viewBag = $viewBag;
        
        if($arg && is_string($arg)){
            $this->httpContext->getRequest()->getRouteData()->set('action', $arg);
        }
        if($arg && is_array($arg)){
            $this->viewBag->merge($arg);
        }
        if($arg && is_object($arg)){
            $this->httpContext->getRequest()->getRouteData()->set('action', $arg->getViewName());
            $this->viewBag->merge($arg->getParams());
        }
        
    }
    
    public function setViewName($name){
        $this->httpContext->getRequest()->getRouteData()->set('action', $name);
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