<?php

namespace System\Web\Mvc;

class RedirectResult extends ActionResult {
    
    protected $httpContext;
    protected $location;
    protected $absolutePath;
    protected $https = false;
    
    public function __construct(\System\Web\HttpContext $httpContext, $location){
        if(is_string($location)){
            $this->httpContext = $httpContext;
            $this->location = $location;
        }
    }
    
    public function absolutePath($bool){
        $this->absolutePath = $bool;
        return $this;
    }
    
    public function https($bool){
        $this->https = $bool;
        return $this;
    }
    
    public function execute(){
        if($this->absolutePath){
            $host = $this->httpContext->getRequest()->getServer('HTTP_HOST');
            $port = $this->httpContext->getRequest()->getServer('SERVER_PORT');
            
            $location = (($this->https)? 'https' : 'http') . '://' . $host;
            if((int)$port != 80){
                $location .= ':'.$port;
            }
            $location .= '/'.ltrim($this->location, '/');
        }else{
            $location = $this->location;
        }
        return $this->httpContext->getResponse()->redirect($location, false);
    }
}