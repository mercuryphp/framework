<?php

namespace System\Web\Routing;

interface IRouteHandler {
    public function execute(\System\Web\HttpRequest $httpRequest, $route, $defaults = array());
}