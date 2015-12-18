<?php

namespace System\Data\Entity;

class EntityNotFoundException extends \Exception {
    protected $entityName;
    protected $object;
    
    public function __construct($entityName, $object){
        parent::__construct(sprintf("The entity '%s' does not exist.", $entityName));
        $this->entityName = $entityName;
        $this->object = $object;
    }
    
    public function getEntityName(){
        return $this->entityName;
    }
    
    public function getObject(){
        return $this->object;
    }
}

