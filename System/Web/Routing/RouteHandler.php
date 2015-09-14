<?php

namespace System\Web\Routing;

use System\Std\Str;
use System\Collections\Dictionary;

class RouteHandler implements IRouteHandler {
    
    public function execute(\System\Web\HttpRequest $httpRequest, $route, $defaults = array()){
        $defaults = new Dictionary($defaults);
        $uri = $httpRequest->getUri();

        $tokens = Str::set($route)->tokenize('{', '}');

        $uriPattern = $uri;
        foreach($tokens as $token){
            $uriPattern = str_replace($token, '@', $uriPattern);
        }

        $uriSegments = Str::set($uriPattern)->split('@');

        $counter=0;
        foreach($tokens as $idx=>$token){ 
            if(substr($token, 0,1) == '{'){
                $tokenName = Str::set($token)->get('{', '}');
                $tokens[$idx] = $uriSegments->get($counter);
                
                if($tokens[$idx]){
                    $defaults->set((string)$tokenName, $tokens[$idx]);
                }
                ++$counter;
            }
        }

        if($uri == trim(join('', $tokens), '/')){
            $httpRequest->getRouteData()->merge($defaults);
            $httpRequest->getParam()->merge($defaults);
            return $httpRequest->getRouteData();
        }
        return false;
    }
}