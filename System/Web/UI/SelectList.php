<?php

namespace System\Web\UI;

class SelectList {
    
    protected $data = array();
    protected $dataValue;
    protected $dataText;
    protected $selectedValue;
    
    public function __construct(array $source, $dataValue, $dataText, $selectedValue = null){
        
        $this->dataValue = $dataValue;
        $this->dataText = $dataText;
        $this->selectedValue = $selectedValue;

        foreach($source as $item){
            if(is_object($item) && !$item instanceof \stdClass){
                $refClass = new \ReflectionClass($item);
                $properties = $refClass->getProperties();

                foreach($properties as $property){
                    $property->setAccessible(true);
                    $name = $property->getName();
                    $value = $property->getValue($item);
                    $array[$name] = $value;
                }
                $this->data[] = $array;
                
            }elseif(is_object($item) && $item instanceof \stdClass){
                $this->data[] = get_object_vars($item);
            }elseif(is_array($item)){
                $this->data[] = $item;
            }
        }
    }
    
    public function getDataValue(){
        return $this->dataValue;
    }
    
    public function getDataText(){
        return $this->dataText;
    }
    
    public function getSelectedValue(){
        return $this->selectedValue;
    }
    
    public function getData(){
        return $this->data;
    }
}

?>