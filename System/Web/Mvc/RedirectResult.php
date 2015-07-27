<?php

namespace System\Web\Mvc;

class RedirectResult extends ActionResult {
    
    protected $response;
    protected $location;
    
    public function __construct(\System\Web\HttpResponse $response, $location){
        if(is_string($location)){
            $this->response = $response;
            $this->location = $location;
        }
    }
    
    public function execute(){
        return $this->response->redirect($this->location, false);
    }
}