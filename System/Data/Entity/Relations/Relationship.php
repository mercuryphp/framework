<?php

namespace System\Data\Entity\Relations;

abstract class Relationship {

    protected $db;
    protected $principalEntityName;
    protected $dependantEntity;
    protected $bindingProperties;
    protected $relationships;
    protected $relationshipType;
    protected $singleOrDefault = false;
    protected $eagerLoading = true;

    /**
     * Sets the database instance. This method is not intended to be 
     * used directly.
     * 
     * @param   System.Data.Database $db
     * @return  void
     */
    public function setDatabase(\System\Data\Database $db){
        $this->db = $db;
    }
    
    /**
     * Sets the EntityMetaCollection. This method is not intended to be 
     * used directly.
     * 
     * @return  void
     */
    public function setMetaCollection(\System\Data\Entity\EntityMetaCollection $metaCollection){
        $this->metaCollection = $metaCollection;
    }

    /**
     * Sets the dependant entity object. This method is not intended to be 
     * used directly.
     * 
     * @return  void
     */
    public function setDependantEntity($dependantEntity){
        $this->dependantEntity = $dependantEntity;
    }

    /**
     * Gets an array of child relationships.
     * 
     * @return  array
     */
    public function getChildRelationships(){
        return $this->relationships;
    }
    
    /**
     * Gets the principal entity name.
     * 
     * @return  array
     */
    public function getPrincipalEntityName(){
        return $this->principalEntityName;
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
        $this->relationships[$propertyName] = $relationship;
    }
    
    /**
     * Gets the result of the current relationship query. If eager loading is 
     * set to true, then the return type is an object, which contains the result 
     * data. If eager loading is set to false, then gets a cloure, which 
     * encapsulates an instance of QueryBuilder.
     * 
     * @return  mixed
     */
    public function execute(){
        
        $params = array();
        $sqlSelect = new \System\Data\Entity\QueryBuilder(new \System\Data\Entity\SqlQuery($this->db, $this->metaCollection), $this->principalEntityName, \System\Data\Entity\QueryBuilder::SELECT);
        $sqlSelect->setFields('*');
        $meta = $this->metaCollection->get(str_replace('\\','.',get_class($this->dependantEntity)));
        $dependantKeyName = $meta->getKey()->getKeyName();

        if(count($this->bindingProperties) == 0){
            $this->bindingProperties = array($dependantKeyName => $dependantKeyName);
        }
 
        $dependantEntityProperties = \System\Std\Object::getProperties($this->dependantEntity); 
        foreach($this->bindingProperties as $principalProperty => $dependantProperty){
            if(array_key_exists($dependantProperty, $dependantEntityProperties)){ 
                $value = $dependantEntityProperties[$dependantProperty];
                $sqlSelect->where($principalProperty.'=:'.$principalProperty);
                $params[$principalProperty] = $value;
            }
        }

        $entityType = (count($this->relationships) > 0) ? $this : $this->principalEntityName;

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
