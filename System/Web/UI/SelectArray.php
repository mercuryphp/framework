<?php

namespace System\Web\UI;

class SelectArray {
    
    protected $data = array();
    protected $selectedValue;
    
    public function __construct(array $source, $selectedValue = null, $useIndexAsValue = false){
        $this->selectedValue = $selectedValue;

        foreach($source as $idx => $item){
            $value = $useIndexAsValue ? $idx : $item;
            $this->data[] = array('value' => $value,'text' => $item);
        }
    }

    public function getDataValue(){
        return 'value';
    }
    
    public function getDataText(){
        return 'text';
    }
    
    public function getSelectedValue(){
        return $this->selectedValue;
    }
    
    public function getData(){
        return $this->data;
    }
}