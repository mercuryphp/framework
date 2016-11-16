<?php

namespace System\Web\Mvc;

class ViewArg {
    
    protected $view;
    protected $params;

    public function __construct($view, array $params = []){
        $this->view = $view;
        $this->params = $params;
    }
    
    public function getViewName(){
        return $this->view;
    }
    
    public function getParams(){
        return $this->params;
    }
}

