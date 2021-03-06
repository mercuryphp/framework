<?php

namespace System\Web\Mvc;

class ViewEngineLayoutAttribute extends \System\Web\Mvc\PreActionAttribute {

    protected $layout = array();
    
    public function __construct($layout = ''){
        $this->layout = $layout;
    }
    
    public function execute(\System\Web\Mvc\Controller $controller, \System\Collections\Dictionary $actionArgs){
        $controller->getViewEngine()->setLayout($this->layout);
    }
}