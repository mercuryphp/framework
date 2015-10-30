<?php

namespace System\Web\Security;

class AuthorizeAttribute extends \System\Web\Mvc\FilterAttribute {

    public function isValid(\System\Web\HttpContext $httpContext){
        if($httpContext->getRequest()->getUser()->isAuthenticated()){
            return true;
        }
        return false;
    }
}