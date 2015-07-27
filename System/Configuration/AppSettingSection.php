<?php

namespace System\Configuration;

final class AppSettingSection extends \System\Collections\Dictionary {

    public function __construct($section){
        parent::__construct($section);
        $this->isReadOnly = true;
    }
}