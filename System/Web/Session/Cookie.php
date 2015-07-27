<?php

namespace System\Web\Session;

class Cookie extends Session {
    
    public function open(){
        if($this->httpRequest->getCookies()->hasKey($this->sessionName)){
            $string = $this->httpRequest->getCookies()->get($this->sessionName)->getValue();
            $this->collection = unserialize($string);
        }
    }
    
    public function write(){ //print "h"; exit;
        if($this->sessionStarted || $this->sliding){
            $httpCookie = new \System\Web\HttpCookie($this->sessionName, serialize($this->collection), $this->expires, $this->path, $this->domain, $this->isSecure, $this->isHttpOnly);
            $this->httpResponse->getCookies()->add($httpCookie);
        }
    }
}