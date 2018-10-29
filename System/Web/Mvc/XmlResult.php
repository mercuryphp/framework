<?php

namespace System\Web\Mvc;

class XmlResult extends ActionResult {
    
    protected $httpResponse;
    protected $data;
    protected $options;
    
    public function __construct(\System\Web\HttpResponse $httpResponse, $data, $options){
        $this->httpResponse = $httpResponse;
        $this->data = $data;
        $this->options = $options;
    }
    
    public function execute(){
        $rootName = isset($this->options['rootName']) ? $this->options['rootName'] : 'root';
        $encoding = isset($this->options['encoding']) ? $this->options['encoding'] : 'UTF-8';
                
        $xml = simplexml_load_string("<?xml version='1.0'?><".$rootName."></".$rootName.">"); 
        $xmlData = $this->toXml($this->data, $xml)->asXml();
        
        $this->httpResponse
            ->setContentType('application/xml')
            ->setContentEncoding($encoding)
            ->setContentLength(strlen($xmlData));

        return $xmlData;
    }
    
    private function toXml($collection, $rootNode){
        foreach($collection as $key => $value){
            $key = is_numeric($key) ? 'item' : $key;
            if(is_array($value)){
                $subNode = $rootNode->addChild($key);            
                $this->toXml($value, $subNode);
            }elseif (is_object($value)){
                if(isset($this->options['childName'])){
                    $childName = $this->options['childName'];
                }else{
                    $segments = explode('\\',get_class($value));
                    $childName = array_pop($segments);
                }
                $subNode = $rootNode->addChild($childName);            
                $this->toXml(\System\Std\Obj::getProperties($value), $subNode);
            }else{
                $rootNode->addChild($key,htmlentities($value));  
            }
        }
        return $rootNode;
    }
}