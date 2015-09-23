<?php

namespace System\Data\Entity\Relations;

abstract class Relationship {

    protected $dbContext;
    protected $metaReader;
    protected $entityName;
    protected $bindingParams;
    protected $relationships;
    protected $relationshipType;
    
    public function setDbContext($db){
        $this->dbContext = $db;
    }

    public function setParentEntity($entity){
        $this->parentEntity = $entity;
    }
    
    public function add($propertyName, $relationships){
        $this->relationships[$this->entityName.':'.$propertyName] = $relationships;
    }

    public function bind(){
        
        $params = array();
        $sqlSelect = new \System\Data\Entity\SelectQuery(new \System\Data\Entity\SqlQuery($this->dbContext), '*', $this->entityName);
        $meta = $this->dbContext->getMetaReader()->read(str_replace('\\','.',get_class($this->parentEntity)));
        $dependantKeyName = $meta->getKey()->getKeyName();
        
        if(count($this->bindingParams) == 0){
            $this->bindingParams = array($dependantKeyName => $dependantKeyName);
        }
        
        foreach($this->bindingParams as $principalProperty => $dependantProperty){
            $value = \System\Std\Object::getPropertyValue($this->parentEntity, $dependantProperty);
            $sqlSelect->where($principalProperty.'=:'.$principalProperty);
            $params[$principalProperty] = $value;
        }
        
        $entityType = (count($this->relationships) > 0) ? $this->relationships : $this->entityName;

        switch($this->relationshipType){
            case 1:
                return $sqlSelect->single($params, $entityType);
            case 2:
                return $sqlSelect->toList($params, $entityType);
        }
    }
}
