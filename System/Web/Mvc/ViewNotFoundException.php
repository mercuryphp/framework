<?php

namespace System\Web\Mvc;

class ViewNotFoundException extends \Exception {
    
    protected $viewFile;
    
    public function __construct($message, $viewFile) {
        $this->viewFile = $viewFile;
        parent::__construct(sprintf($message, $viewFile));
    }
    
    public function getViewFile(){
        return $this->viewFile;
    }
}