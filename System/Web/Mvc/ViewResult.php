<?php

namespace System\Web\Mvc;

class ViewResult extends ActionResult {
    
    protected $controller;
    protected $viewContext;
    
    public function __construct(\System\Web\Mvc\Controller $controller, $viewContext){
        $this->controller = $controller;
        $this->viewContext = $viewContext;
    }
    
    public function setViewName($name){
        $this->viewContext->setViewName($name);
        return $this;
    }
    
    public function execute(){
        return $this->controller->getViewEngine()->render($this->viewContext);
    }
}