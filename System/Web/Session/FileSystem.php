<?php

namespace System\Web\Session;

class FileSystem extends Session {
    
    protected $sessionFile;
    
    public function open(){
        if($this->httpRequest->getCookies()->hasKey($this->sessionName)){
            $this->sessionId = $this->httpRequest->getCookies()->get($this->sessionName)->getValue();
            $this->sessionFile = session_save_path() .'/sess_' . $this->sessionId;
            
            if(is_file($this->sessionFile)){
                $this->collection = unserialize(file_get_contents($this->sessionFile));
            }
        }
    }
    
    public function write(){ 
        if($this->sessionStarted){
            if(!$this->sessionFile){
                $this->sessionId = sha1(uniqid(mt_rand()));
                $this->sessionFile = session_save_path() .'/sess_' . $this->sessionId;
            }

            $httpCookie = new \System\Web\HttpCookie($this->sessionName, $this->sessionId, $this->expires, $this->path, $this->domain, $this->isSecure, $this->isHttpOnly);
            $this->httpResponse->getCookies()->add($httpCookie);

            file_put_contents($this->sessionFile, serialize($this->collection));
        }
    }
}