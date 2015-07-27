<?php

namespace System\Web\Routing;

use System\Std\String;
use System\Collections\Dictionary;

class RouteHandler {
    
    public function execute($httpRequest, $route, $defaults = array()){
        $defaults = new Dictionary($defaults);
        $uri = $httpRequest->getUri();

        $token = '';
        $tokens = array();
        
        for($i=0; $i < strlen($route); $i++){
            $char = $route[$i];

            if($char=='{'){
                $tokens[] = $token;
                $token = '';
                continue;

            }
            if($char=='}'){
                $tokens[] = '{'.$token.'}';
                $token = '';
                continue;
            }

            $token.= $char;
            
            if($i==strlen($route)-1){
                $tokens[] = $token;
            }
        }

        $uriPattern = $uri;
        foreach($tokens as $token){
            $uriPattern = str_replace($token, '@', $uriPattern);
        }

        $uriSegments = String::set($uriPattern)->split('@');

        $counter=0;
        foreach($tokens as $idx=>$token){ 
            if(substr($token, 0,1) == '{'){
                $tokenName = String::set($token)->get('{', '}');
                $tokens[$idx] = $uriSegments->get($counter);
                
                if($tokens[$idx]){
                    $defaults->set((string)$tokenName, $tokens[$idx]);
                }
                
                ++$counter;
            }
        }

        $httpRequest->getRouteData()->merge($defaults);

        if($uri == trim(join('', $tokens), '/')){
            return $httpRequest->getRouteData();
        }
        return false;
    }
}