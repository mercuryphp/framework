<?php

namespace System\Configuration;

class ConnectionStringSection extends \System\Collections\Dictionary {

    public function __construct($section){
        parent::__construct($section);
        $this->isReadOnly = true;
    }
}