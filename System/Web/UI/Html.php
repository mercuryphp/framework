<?php

namespace System\Web\UI;

class Html {
    public static function dropdown($name, $source, $default = null, array $attributes = array()){
        $dropDown = new DropDown($name, $source, $default, $attributes);
        echo $dropDown->render();
    }
    
    public static function textbox($name, $value = '', array $attributes = array(), $textMode = 'text'){
        $textbox = new TextBox($name, $value, $attributes, $textMode);
        echo $textbox->render();
    }
    
    public static function password($name, $value = '', array $attributes = array(), $textMode = 'password'){
        $textbox = new TextBox($name, $value, $attributes, $textMode);
        echo $textbox->render();
    }
    
    public static function textarea($name, $value = '', array $attributes = array(), $textMode = 'textarea'){
        $textbox = new TextBox($name, $value, $attributes, $textMode);
        echo $textbox->render();
    }
    
    public static function selectList(array $source, $dataValue, $dataText, $selectedValue = null){
        return new \System\Web\UI\SelectList($source, $dataValue, $dataText, $selectedValue);
    }
}

?>