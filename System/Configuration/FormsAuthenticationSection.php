<?php

namespace System\Configuration;

class FormsAuthenticationSection extends \System\Collections\Dictionary {

    public function __construct($section){

        $defaults = array(
            'cookieName' =>'PHPXAUTH',
            'hashAlgorithm' => 'sha256',
            'cipher' => MCRYPT_RIJNDAEL_256,
            'encryptionKey' => '',
            'validationKey' => ''
        );
        
        $this->merge($defaults)->merge($section);
        $this->isReadOnly = true;
    }
    
    public function getCookieName(){
        return $this->collection['cookieName'];
    }
    
    public function getHashAlgorithm(){
        return $this->collection['hashAlgorithm'];
    }
    
    public function getCipher(){
        return $this->collection['cipher'];
    }
    
    public function getEncryptionKey(){
        return $this->collection['encryptionKey'];
    }
    
    public function getValidationKey(){
        return $this->collection['validationKey'];
    }
}