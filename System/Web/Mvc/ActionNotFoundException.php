<?php

namespace System\Web\Mvc;

class ActionNotFoundException extends HttpException {
    public function __construct(\System\Web\HttpContext $httpContext){
        $routeData = $httpContext->getRequest()->getRouteData();
        $httpContext->getResponse()->setStatusCode(404)->flush();
        parent::__construct(sprintf("The action '%s' does not exist in '%sController'", $routeData->get('action'), ucfirst(strtolower($routeData->get('controller')))));
    }
}