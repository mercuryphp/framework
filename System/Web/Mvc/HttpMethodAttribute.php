<?php

namespace System\Web\Mvc;

class HttpMethodAttribute extends FilterAttribute {
    
    protected $methods = array();
    
    public function __construct(){
        $this->methods = func_get_args();
    }
    
    public function isValid(\System\Web\HttpContext $httpContext){
        if(!in_array($httpContext->getRequest()->getHttpMethod(), $this->methods)){
            throw new ActionNotFoundException($httpContext);
        }
    }
}

