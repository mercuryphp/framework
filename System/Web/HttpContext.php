<?php

namespace System\Web;

class HttpContext {
    
    private $request;
    private $response;
    private $session;
    
    public function __construct(HttpRequest $request, HttpResponse $response, Session\Session $session){
        $this->request = $request;
        $this->response = $response;
        $this->session = $session;
    }
    
    public function setRequest(HttpRequest $request){
        $this->request = $request;
    } 

    public function getRequest(){
        return $this->request;
    } 
    
    public function setResponse(HttpResponse $response){
        $this->response = $response;
    }
    
    public function getResponse(){
        return $this->response;
    }  
    
    public function setSession(Session\Session $session){
        $this->session = $session;
    }
    
    public function getSession(){
        return $this->session;
    } 
}

?>