<?php

namespace System\Web\UI;

class DropDown extends Element {
    
    protected $source;
    
    public function __construct($name, $source, array $attributes = array()){
        parent::__construct();
        
        $attributes['name'] = $name;
        $attributes['id'] = $name;
        
        $this->source = $source;
        $this->attributes = array_merge($this->attributes, $attributes);
    }
    
    public function render(){
        $data = $this->source->getData();
        $dataValue = $this->source->getDataValue();
        $dataText = $this->source->getDataText();
        $selectedValue = $this->source->getSelectedValue();

        $control = $this->control->append('<select ')->append($this->renderAttributes())->append('>');

        foreach($data as $item){
            $control = $control->append('<option value="')
                ->append($item[$dataValue])
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

?>