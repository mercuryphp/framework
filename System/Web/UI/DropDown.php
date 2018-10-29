<?php

namespace System\Web\UI;

class DropDown extends Element {
    
    protected $source;
    protected $default;
    
    public function __construct($name, $source, $default = null, array $attributes = array()){
        parent::__construct();
        
        $attributes['name'] = !array_key_exists('name',$attributes) ? $name : $attributes['name'];
        $attributes['id'] = !array_key_exists('id',$attributes) ? str_replace(['.','[',']',], '_', $name) : str_replace(['.','[',']'], '_', $attributes['id']);
        
        $this->source = $source;
        $this->default = $default;
        $this->attributes = array_merge($this->attributes, $attributes);
    }
    
    public function render(){
        $data = $this->source->getData();
        $dataValue = $this->source->getDataValue();
        $dataText = $this->source->getDataText();
        $selectedValue = $this->source->getSelectedValue();
        
        if(is_object($selectedValue)){
            $selectedValue = \System\Std\Obj::getPropertyValue($selectedValue, $this->attributes['name']);
        }

        $control = $this->control->append('<select ')->append($this->renderAttributes())->append('>');

        if(!is_null($this->default)){
            array_unshift($data, array($dataValue => $this->default, $dataText => $this->default));
        }

        foreach($data as $item){
            $control = $control->append('<option value="')
                ->append($this->escape($item[$dataValue]))
                ->append('" ');
            
            if($selectedValue == $item[$dataValue]){
                $control = $control->append('selected');
            }
            
            if(is_callable($dataText)){
                $text = call_user_func($dataText, $item);
            }else{
                $text = $item[$dataText];
            }
            
            $control = $control->append('>')
                ->append($text)
                ->append('</option>');
        }
        
        return $control->append('</select>')->toString();
    }
}