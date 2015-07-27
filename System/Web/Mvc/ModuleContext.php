<?php

namespace System\Web\Mvc;

class ModuleContext {
    
    protected $config;
    protected $controller;
    protected $httpContext;
    
    public function __construct(
    \System\Configuration\Configuration $config,
            \System\Web\Mvc\Controller $controller,
            \System\Web\HttpContext $httpContext
            ){
        
        $this->config = $config;
        $this->controller = $controller;
        $this->httpContext = $httpContext;
    }
    
    public function getConfig(){
        return $this->config;
    }
    
    public function getController(){
        return $this->controller;
    }
    
    public function getHttpContext(){
        return $this->httpContext;
    }
}