<?php

namespace System\Web;

final class HttpContext {
    
    private $request;
    private $response;
    private $session;
    
    public function __construct($request, $response, $session){
        $this->request = $request;
        $this->response = $response;
        $this->session = $session;
    }

    public function getRequest(){
        return $this->request;
    } 
    
    public function getResponse(){
        return $this->response;
    }  
    
    public function getSession(){
        return $this->session;
    } 
}

?>
