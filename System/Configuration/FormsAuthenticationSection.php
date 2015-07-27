<?php

namespace System\Configuration;

class FormsAuthenticationSection extends \System\Collections\Dictionary {

    public function __construct($section){

        $defaults = array(
            'cookieName' =>'PHPXAUTH',
            'encryptionKey' => '',
            'validationKey' => ''
        );
        
        $this->merge($defaults)->merge($section);
        $this->isReadOnly = true;
    }
    
    public function getCookieName(){
        return $this->collection['cookieName'];
    }
    
    public function getEncryptionKey(){
        return $this->collection['encryptionKey'];
    }
    
    public function getValidationKey(){
        return $this->collection['validationKey'];
    }
}