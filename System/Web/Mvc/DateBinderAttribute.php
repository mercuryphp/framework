<?php

namespace System\Web\Mvc;

class DateBinderAttribute extends ModelBinder {

    protected $culture;
    
    public function __construct($paramName, $culture = null){
        $this->paramName = $paramName;
        $this->culture = trim($culture);
    }

    public function bind(\System\Web\Mvc\ModelBindingContext $modelBindingContext){
        if(!$modelBindingContext->isParamOptional() && !$modelBindingContext->getRequest()->getParam()->get($this->paramName)){
            return \System\Std\Date::now();
        }elseif($modelBindingContext->isParamOptional() && !$modelBindingContext->getRequest()->getParam()->get($this->paramName)){
            return null;
        }
        $date = \System\Std\Date::parse($modelBindingContext->getRequest()->getParam($this->paramName));
        
        if($this->culture){
            $date->setCulture(new \System\Globalization\CultureInfo($this->culture));
        }
        return $date;
    }
}