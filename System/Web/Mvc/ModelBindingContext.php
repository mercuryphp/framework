<?php

namespace System\Web\Mvc;

class ModelBindingContext {
    
    protected $httpRequest;
    protected $objectName;
    protected $isParamOptional;
    protected $defaultValue;
    
    public function __construct(\System\Web\HttpRequest $httpRequest, $objectName, $isParamOptional, $defaultValue){
        $this->httpRequest = $httpRequest;
        $this->objectName = $objectName;
        $this->isParamOptional = $isParamOptional;
        $this->defaultValue = $defaultValue;
    }

    public function getRequest(){
        return $this->httpRequest;
    }
    
    public function getObjectName(){
        return $this->objectName;
    }
    
    public function getDefaultValue(){
        return $this->defaultValue;
    }
    
    public function isParamOptional(){
        return $this->isParamOptional;
    }
}