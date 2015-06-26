<?php

namespace System\Data\Entity;

class EntityContext {
    
    protected $entityHash;
    protected $entityName;
    protected $entity;
    protected $state = 1;

    const PERSIST = 1;
    const PERSISTED = 2;
    
    public function __construct($entity){
        $this->entityHash = spl_object_hash($entity);
        $this->entityName = \System\Std\String::set(get_class($entity))->trim('\\')->replace('\\', '.')->toString();
        $this->entity = $entity;
    }
    
    public function getEntity(){
        return $this->entity;
    }
    
    public function setState($state){
        $this->state = $state;
    }

    public function getState(){
        return $this->state;
    }
    
    public function getName(){
        return $this->entityName;
    }
    
    public function getHashCode(){
        return $this->entityHash;
    }
}

?>