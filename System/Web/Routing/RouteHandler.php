<?php

namespace System\Web\Routing;

class RouteHandler {
    
    public function execute($httpRequest, $route, $defaults = array()){
        $routeData = new \System\Collections\Dictionary($defaults);
        $httpRequest->setRouteData($routeData);
        $uri = $httpRequest->getUri();

        if(!$uri){
            return $routeData;
        }

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
            $routeData->set($token, $value);
            $httpRequest->setParam($token, $value);
            $route = str_replace('{'.$token.'}', $routeData[$token], $route);
        }

        if($uri == $route){
            return $routeData;
        }
        return false;
    }
}

?>
