<?php

namespace System\Web\UI;

class Html {
    public static function dropdown($name, $source, $default = null, array $attributes = array()){
        $control = new DropDown($name, $source, $default, $attributes);
        echo $control->render();
    }
    
    public static function textbox($name, $value = '', array $attributes = array(), $textMode = 'text'){
        $control = new TextBox($name, $value, $attributes, $textMode);
        echo $control->render();
    }
    
    public static function password($name, $value = '', array $attributes = array(), $textMode = 'password'){
        $control = new TextBox($name, $value, $attributes, $textMode);
        echo $control->render();
    }
    
    public static function textarea($name, $value = '', array $attributes = array(), $textMode = 'textarea'){
        $control = new TextBox($name, $value, $attributes, $textMode);
        echo $control->render();
    }
    
    public static function link($title, $href, array $attributes = array(), $params = null){
        $control = new Link($title, $href, $attributes, $params);
        echo $control->render();
    }
    
    public static function label($text, array $attributes = array()){
        $control = new Label($text, $attributes);
        echo $control->render();
    }
    
    public static function hidden($text, $value, array $attributes = array()){
        $control = new Hidden($text, $value, $attributes);
        echo $control->render();
    }
    
    public static function fileUpload($name, array $attributes = array()){
        $control = new FileUpload($name, $attributes);
        echo $control->render();
    }
    
    public static function table($source, array $attributes = array()){
        return new Table($source, $attributes);
    }
    
    public static function selectList(array $source, $dataValue, $dataText, $selectedValue = null){
        return new SelectList($source, $dataValue, $dataText, $selectedValue);
    }
    
    public static function selectArray(array $source, $selectedValue = null, $useIndexAsValue = false){
        return new SelectArray($source, $selectedValue, $useIndexAsValue);
    }
}

?>