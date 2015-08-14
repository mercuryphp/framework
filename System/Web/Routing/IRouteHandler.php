<?php

namespace System\Web\Routing;

interface IRouteHandler {
    public function execute($httpRequest, $route, $defaults = array());
}