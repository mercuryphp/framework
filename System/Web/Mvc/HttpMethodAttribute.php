<?php

namespace System\Web\Mvc;

class HttpMethodAttribute extends ReturnAttribute {
    
    protected $methods = array();
    
    public function __construct(){
        $this->methods = func_get_args();
    }
    
    public function isValid(\System\Web\HttpContext $httpContext){
        if(in_array($httpContext->getRequest()->getHttpMethod(), $this->methods)){
            return true;
        }
        return false;
    }
}

