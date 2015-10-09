<?php

namespace System\Web\Mvc;

class AuthorizeAttribute extends FilterAttribute {
    
    protected $redirectLocation;
    
    public function __construct($redirectLocation = null){
        $this->redirectLocation = $redirectLocation;
    }

    public final function isValid(\System\Web\HttpContext $httpContext){
        if(!$httpContext->getRequest()->getUser()->isAuthenticated()){
            $this->handle($httpContext);
            return false;
        }
        return true;
    }
    
    public function handle(\System\Web\HttpContext $httpContext){
        if($this->redirectLocation){
            $httpContext->getResponse()->redirect($this->redirectLocation);
        }else{
            $httpContext->getResponse()->setStatusCode(401)->clear()->write('HTTP Error 401 - Unauthorized')->endFlush();
        }
    }
}