<?php

namespace System\Web\Mvc;

class ControllerNotFoundException extends HttpException {
    public function __construct(\System\Web\HttpContext $httpContext){
        $routeData = $httpContext->getRequest()->getRouteData();
        $httpContext->getResponse()->setStatusCode(404)->flush();
        parent::__construct(sprintf("The controller '%sController' does not exist.", ucfirst(strtolower($routeData->get('controller')))));
    }
}