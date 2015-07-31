<?php

namespace System\Web\Mvc;

class JsonResult extends ActionResult {
    
    protected $httpResponse;
    protected $data;
    protected $options;
    
    public function __construct(\System\Web\HttpResponse $httpResponse, $data, $options){
        $this->httpResponse = $httpResponse;
        $this->data = $data;
        $this->options = $options;
    }
    
    public function execute(){
        $jsonData = json_encode($this->data, $this->options); 
        $this->httpResponse
            ->setContentType('application/json')
            ->setContentEncoding('UTF-8')
            ->setContentLength(strlen($jsonData));
        return $jsonData;
    }
}