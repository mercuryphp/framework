<?php

namespace System\Web\Session;

class FileSystem extends Session {
    
    protected $sessionFile;
    
    public function __construct($sessionName, $expire, $path, $domain, $secure, $httpOnly){
        $this->sessionName = $sessionName;
        $this->expire = $expire ? \System\Std\Date::now()->addSeconds($expire)->getTimestamp() : 0;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;

        if(array_key_exists($this->sessionName, $_COOKIE)){
            $this->sessionId = $_COOKIE[$this->sessionName];
            $this->sessionFile = session_save_path() .'/sess_' . $this->sessionId;
            
            if(is_file($this->sessionFile)){
                $this->collection = unserialize(file_get_contents($this->sessionFile));
            }
        }
    }
    
    public function writeSession(){ 
        if($this->sessionStarted){
            if(!$this->sessionFile){
                $this->sessionId = sha1(uniqid(mt_rand()));
                $this->sessionFile = session_save_path() .'/sess_' . $this->sessionId;
            }

            setcookie($this->sessionName, $this->sessionId, $this->expire, $this->path, $this->domain, $this->secure, $this->httpOnly);
            file_put_contents($this->sessionFile, serialize($this->collection));

            if(is_callable($this->onSaveFunction)){
                call_user_func($this->onSaveFunction);
            }
        }
    }
}

