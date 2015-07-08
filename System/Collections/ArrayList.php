<?php

namespace System\Collections;

class ArrayList extends Collection implements IList {
    
    public function add($value){
        $this->collection[] = $value;
    }
}

?>
