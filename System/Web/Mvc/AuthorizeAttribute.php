<?php

namespace System\Web\Mvc;

class AuthorizeAttribute extends FilterAttribute {
    
    protected $redirectLocation;
    
    /**
     * Initializes an instance of AuthorizeAttribute with an optional
     * redirect location.
     * 
     * @param   string $redirectLocation = null
     */
    public function __construct($redirectLocation = null){
        $this->redirectLocation = $redirectLocation;
    }

    /**
     * Gets a boolean value that determines if the request user is authenticated.
     * 
     * @param   System.Web.HttpContext $httpContext
     * @return  bool
     */
    public final function isValid(\System\Web\HttpContext $httpContext){
        if(!$httpContext->getRequest()->getUser()->isAuthenticated()){
            $this->handle($httpContext);
            return false;
        }
        return true;
    }
    
    /**
     * Handles unauthorized requests. If $redirectLocation was specified, then
     * redirects the user to $redirectLocation. If $redirectLocation
     * was not specified, a 401 Unauthorized header is sent to the client.
     * 
     * @param   System.Web.HttpContext $httpContext
     * @return  void
     */
    public function handle(\System\Web\HttpContext $httpContext){
        if($this->redirectLocation){
            $httpContext->getResponse()->redirect($this->redirectLocation);
        }else{
            $httpContext->getResponse()->setStatusCode(401)->clear()->write('HTTP Error 401 - Unauthorized')->endFlush();
        }
    }
}