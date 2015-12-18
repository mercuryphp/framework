<?php

namespace System\Web\Mvc;

class ParamBinderAttribute extends ModelBinder {
    
    public function __construct($paramName){
        $this->paramName = $paramName;
    }

    public function bind(\System\Web\Mvc\ModelBindingContext $modelBindingContext){
        if(!$modelBindingContext->isParamOptional() && !$modelBindingContext->getRequest()->getQueryString()){
            return new \System\Collections\Dictionary();
        }elseif($modelBindingContext->isParamOptional() && !$modelBindingContext->getRequest()->getQueryString()){  
            return null;
        }
        return \System\Std\Str::set($modelBindingContext->getRequest()->getQueryString())->parameterize()->merge($modelBindingContext->getRequest()->getQuery());
    }
}