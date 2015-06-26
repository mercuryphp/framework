<?php

namespace System\Web\UI;

class Html {
    public static function dropdown($name, $source, array $attributes = array()){
        $dropDown = new DropDown($name, $source, $attributes);
        echo $dropDown->render();
    }
    
    public static function textbox($name, $value = '', array $attributes = array()){
        $textbox = new TextBox($name, $value, $attributes);
        echo $textbox->render();
    }
}

?>