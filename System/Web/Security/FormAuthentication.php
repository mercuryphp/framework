<?php

namespace System\Web\Security;

class FormAuthentication {
    
    protected $httpContext;
    protected $cookieName = 'PHPXAUTH';
    protected $hashAlgorithm = 'sha256';
    protected $cipher = MCRYPT_RIJNDAEL_256;
    protected $validationKey;
    protected $encryptionKey;
    
    public function __construct($validationKey, $encryptionKey){
        $this->validationKey = $validationKey;
        $this->encryptionKey = $encryptionKey;
    }
    
    public function setCookieName($cookieName){
        $this->cookieName = $cookieName;
    }

    public function setHttpContext(\System\Web\HttpContext $httpContext){
        $this->httpContext = $httpContext;
    }
    
    public function setTicket(AuthenticationTicket $ticket){
        
        if(!$this->validationKey || !$this->encryptionKey){
            throw new \RuntimeException('A validationKey and encryptionKey is required with FormAuthentication.');
        }
        $data = array(
            $ticket->getName(),
            $ticket->getExpire(),
            $ticket->getUserData(),
        );
        
        $string = serialize($data);
        $hash = Security::hmac($this->hashAlgorithm, $string, $this->validationKey);
        $iv = Security::iv(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC, MCRYPT_DEV_URANDOM, false);
        $cookieValue = Security::encrypt($this->cipher, $this->encryptionKey, $hash.'|'.$string, MCRYPT_MODE_CBC, $iv, true).'|'.base64_encode($iv);
        $this->httpContext->getResponse()->getCookies($this->cookieName)->setValue($cookieValue);
    }
    
    public function authenticate(){
        if($this->httpContext->getRequest()->getCookies()->hasKey($this->cookieName)){
            $cookie = $this->httpContext->getRequest()->getCookies($this->cookieName); 
        }
    }
}