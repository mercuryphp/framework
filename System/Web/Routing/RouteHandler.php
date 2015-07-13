<?php

namespace System\Web\Routing;

class RouteHandler {
    
    public function execute($httpRequest, $route, $defaults = array()){
        $uri = $httpRequest->getUri();

        $openParen = false;
        $token = '';
        $tokens = array();
        
        for($i=0; $i< strlen($route); $i++){
            $char = $route[$i];

            if($char=='{'){
                $openParen = true;
                continue;
            }
            if($char=='}'){
                $openParen = false;
                $tokens[] = $token;
                $token = '';
            }
            
            if($openParen){
                $token.=$char;
            }
        }
        
        if(!in_array('controller', $tokens) || !in_array('controller', $tokens)){
            throw new \RuntimeException(sprintf("The route '%s' is invalid. A route must contain a controller and action token.", $route));
        }
        
        $routePattern = str_replace($tokens, '@', $route);
        $routeSegments = explode('{@}', $routePattern);
        
        $uriPattern = str_replace($routeSegments, '@', $uri);
        $uriSegments = explode('@', $uriPattern);

        foreach($tokens as $idx=>$token){
            $value = isset($uriSegments[$idx]) ? $uriSegments[$idx] : null;
            $httpRequest->getRouteData()->set($token, $value);
            $httpRequest->setParam($token, $value);
            
            $routeValue = $httpRequest->getRouteData()->get($token);
            $route = str_replace('{'.$token.'}', $routeValue, $route);
        }

        if($uri == trim($route, '/')){
            
            if(!$httpRequest->getRouteData()->get('module')){
                $module = isset($defaults['module']) ? $defaults['module'] : 'Index';
                $httpRequest->getRouteData()->set('module', $module);
            }
            
            if(!$httpRequest->getRouteData()->get('controller')){
                $controller = isset($defaults['controller']) ? $defaults['controller'] : 'Home';
                $httpRequest->getRouteData()->set('controller', $controller);
            }
            
            if(!$httpRequest->getRouteData()->get('action')){
                $action = isset($defaults['action']) ? $defaults['action'] : 'index';
                $httpRequest->getRouteData()->set('action', $action);
            }

            return $httpRequest->getRouteData();
        }
        return false;
    }
}

?>
