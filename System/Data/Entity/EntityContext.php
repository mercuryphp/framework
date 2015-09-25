<?php

namespace System\Data\Entity;

class EntityContext {
    
    protected $entityHash;
    protected $entityName;
    protected $entity;
    protected $state = 1;

    const PERSIST = 1;
    const PERSISTED = 2;
    const DELETE = 3;
    
    /**
     * Initializes an instance of EntityContext that encapsulates an entity 
     * and maintains its state.
     * 
     * @param   object $entity
     */
    public function __construct($entity){
        $this->entityHash = spl_object_hash($entity);
        $this->entityName = \System\Std\Str::set(get_class($entity))->trim('\\')->replace('\\', '.')->toString();
        $this->entity = $entity;
    }
    
    /**
     * Gets the entity object stored in the context.
     * 
     * @return  object
     */
    public function getEntity(){
        return $this->entity;
    }
    
    /**
     * Sets the entity state.
     * 
     * @param   int $state
     * @return  void
     */
    public function setState($state){
        $this->state = $state;
    }

    /**
     * Gets the entity state.
     * 
     * @return  int
     */
    public function getState(){
        return $this->state;
    }
    
    /**
     * Gets the entity type name.
     * 
     * @return  string
     */
    public function getEntityName(){
        return $this->entityName;
    }
    
    /**
     * Gets a unique hash code of the entity stored in the context.
     * 
     * @return  string
     */
    public function getHashCode(){
        return $this->entityHash;
    }
}