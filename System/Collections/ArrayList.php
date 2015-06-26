<?php

namespace System\Collections;

class ArrayList extends Collection implements IList {
    
    public function add($value){
        $this->collection[] = $value;
    }
    
    public function where($value){
        $tmp = array();
        foreach($this->collection as $item){
            if(is_scalar($item)){
                if($item == $value){
                    $tmp[] = $item;
                }
            }
        }
    }
    
    public function whereLike($value){
        $tmp = array(); 
        foreach($this->collection as $item){
            if(is_scalar($item)){
                if(preg_match('@'.$value.'@', $item)){
                    $tmp[] = $item;
                }
            }
        }
        
        print_R($tmp); exit;
    }
}

?>
