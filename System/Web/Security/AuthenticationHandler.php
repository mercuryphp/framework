<?php

namespace System\Web\Security;

abstract class AuthenticationHandler {
    
    protected $httpContext;
    protected $cookieName = 'PHPXAUTH';
    
    public function setCookieName($cookieName){
        $this->cookieName = $cookieName;
    }
    
    public function getCookieName(){
        return $this->cookieName;
    }

    public function setHttpContext(\System\Web\HttpContext $httpContext){
        $this->httpContext = $httpContext;
    }
    
    public abstract function authenticate();
}
