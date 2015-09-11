<?php

namespace System\Data\Entity\Attributes;

class Relationship  {

    protected $entity;
    
    public function __construct($entity){
        $this->entity = $entity;
    }
}