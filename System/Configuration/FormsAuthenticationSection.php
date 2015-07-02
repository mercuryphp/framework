<?php

namespace System\Configuration;

class FormsAuthenticationSection {

    protected $section;
    
    public function __construct($section){
        $defaults = new \System\Collections\Dictionary();
        $defaults->add('cookieName', 'PHPXAUTH')
            ->add('encryptionKey', '')
            ->add('validationKey', '');
        
        $defaults->merge($section);
        $this->section = $defaults;
    }
    
    public function getCookieName(){
        return $this->section->cookieName;
    }
    
    public function getEncryptionKey(){
        return $this->section->encryptionKey;
    }
    
    public function getValidationKey(){
        return $this->section->validationKey;
    }
}

?>