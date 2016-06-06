<?php

namespace System\Web\Mvc;

class ControllerNotFoundException extends HttpException {
    public function __construct(\System\Web\HttpContext $httpContext, $className){
        $routeData = $httpContext->getRequest()->getRouteData(); 
        $httpContext->getResponse()->setStatusCode(404)->flush();
        $file = \System\Std\Environment::getRootPath().'/'.str_replace('.','/',$className).'.php';
        parent::__construct(sprintf("The controller '%sController' does not exist. Class: %s (%s)", ucfirst(strtolower($routeData->get('controller'))), ltrim($className,'.'),$file));
    }
}