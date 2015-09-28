<?php

namespace System\Data\Entity\Relations;

abstract class Relationship {

    protected $db;
    protected $principalEntityName;
    protected $dependantEntity;
    protected $bindingParams;
    protected $relationships;
    protected $relationshipType;
    protected $singleOrDefault = false;
    protected $eagerLoading = true;
    
    public function setDatabase($db){
        $this->db = $db;
    }
    
    /**
     * Sets the EntityMetaCollection.
     * 
     * @return  void
     */
    public function setMetaCollection(\System\Data\Entity\EntityMetaCollection $metaCollection){
        $this->metaCollection = $metaCollection;
    }

    /**
     * Sets the dependant entity object.
     * 
     * @return  void
     */
    public function setDependantEntity($dependantEntity){
        $this->dependantEntity = $dependantEntity;
    }
    
    /**
     * Gets or sets a value indicating if eager loading should be used.
     * 
     * @param   $bool = null
     * @return  mixed
     */
    public function isEagerLoading($bool = null){
        if($bool == null){
            return $this->eagerLoading;
        }
        $this->eagerLoading = $bool;
    }
    
    /**
     * Adds a child relationship that is mapped to a dependant property.
     * 
     * @param   string $propertyName
     * @param   System.Data.Entity.Relations.Relationship $relationship
     * @return  void
     */
    public function add($propertyName, \System\Data\Entity\Relations\Relationship $relationship){
        $this->relationships[$this->principalEntityName.':'.$propertyName] = $relationship;
    }

    /**
     * Binds the result 
     * 
     * @param   string $propertyName
     * @param   System.Data.Entity.Relations.Relationship $relationship
     * @return  mixed
     */
    public function execute(){
        
        $params = array();
        $sqlSelect = new \System\Data\Entity\SelectQuery(new \System\Data\Entity\SqlQuery($this->db, $this->metaCollection), '*', $this->principalEntityName);
        $meta = $this->metaCollection->get(str_replace('\\','.',get_class($this->dependantEntity)));
        $dependantKeyName = $meta->getKey()->getKeyName();
        
        if(count($this->bindingParams) == 0){
            $this->bindingParams = array($dependantKeyName => $dependantKeyName);
        }
        
        $dependantEntityProperties = \System\Std\Object::getProperties($this->dependantEntity);
        foreach($this->bindingParams as $principalProperty => $dependantProperty){
            if(array_key_exists($dependantProperty, $dependantEntityProperties)){ 
                $value = $dependantEntityProperties[$dependantProperty];
                $sqlSelect->where($principalProperty.'=:'.$principalProperty);
                $params[$principalProperty] = $value;
            }
        }

        $entityType = (count($this->relationships) > 0) ? $this->relationships : $this->principalEntityName;

        switch($this->relationshipType){
            case 1:
                if($this->eagerLoading){
                    return $sqlSelect->single($params, $entityType, $this->singleOrDefault);
                }
                return function() use($sqlSelect, $params, $entityType){
                    return $sqlSelect->single($params, $entityType, $this->singleOrDefault);
                };
            case 2:
                if($this->eagerLoading){
                    return $sqlSelect->toList($params, $entityType);
                }
                return function() use($sqlSelect, $params, $entityType){
                    return $sqlSelect->toList($params, $entityType);
                };
        }
    }
}
