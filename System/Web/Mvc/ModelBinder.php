<?php

namespace System\Web\Mvc;

abstract class ModelBinder {
    
    protected $paramName;
    
    public function getParameterName(){
        return $this->paramName;
    }
    
    public abstract function bind(\System\Web\Mvc\ModelBindingContext $modelBindingContext);
}
