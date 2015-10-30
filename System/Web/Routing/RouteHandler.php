<?php

namespace System\Web\Routing;

use System\Std\Str;
use System\Collections\Dictionary;

class RouteHandler implements IRouteHandler {
    
    public function execute(\System\Web\HttpRequest $httpRequest, $route, $defaultArray = array(), $constraints = array()){
        $defaults = new Dictionary($defaultArray);
        $uri = $httpRequest->getUri();

        $tokens = Str::set($route)->tokenize('{', '}');

        $uriPattern = $uri;
        foreach($tokens as $token){
            $uriPattern = preg_replace('#'.$token.'#', '#', $uriPattern, 1);
        }

        $uriSegments = Str::set($uriPattern)->split('#');

        $counter=0;
        foreach($tokens as $idx=>$token){ 
            if(substr($token, 0,1) == '{'){
                $tokenName = Str::set($token)->get('{', '}');
                $tokens[$idx] = $uriSegments->get($counter);

                if($tokens[$idx]){
                    if(array_key_exists((string)$tokenName, $constraints)){ 
                        $constraint = $constraints[(string)$tokenName];

                        if(is_string($constraint)){
                            if(!preg_match($constraint, $tokens[$idx])){
                                return false;
                            }
                        }elseif(is_callable($constraint)){
                            if(!$constraint($tokens[$idx])){
                                return false;
                            }
                        }elseif($constraint instanceof RouteConstraint){
                            if(!$constraint->execute($tokens[$idx])){
                                return false;
                            }
                        }
                    }
                    $defaults->set((string)$tokenName, $tokens[$idx]);
                }
                ++$counter;
            }
        } 

        if($uri == trim($tokens->join(''), '/')){
            $httpRequest->getRouteData()->merge($defaults);
            $httpRequest->getParam()->merge($defaults);
            return $httpRequest->getRouteData();
        }
        return false;
    }
}