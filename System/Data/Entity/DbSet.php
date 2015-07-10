<?php

namespace System\Data\Entity;

class DbSet {
    
    protected $dbContext;
    protected $meta = array();
    protected $entities = array();
    
    public function __construct(DbContext $dbContext, EntityMeta $meta){
        $this->dbContext = $dbContext;
        $this->meta = $meta;
    }
    
    public function find($params, $default = false){

        if(is_scalar($params)){
            $params = array($this->meta->getKey() => $params);
        }
        
        if(is_array($params)){
            $select = $this->dbContext->select('*', $this->meta->getEntityName());
            
            foreach($params as $key=>$param){
                $select->where($key.'=:'.$key);
            }
        
            $entity = $select->single($this->meta->getEntityName(), $params, $default);
            
            $entityContext = $this->add($entity);
            $entityContext->setState(EntityContext::PERSISTED);
            $this->dbContext->getPersistedEntities()->add($entityContext->getHashCode(), $entityContext);
            
            return $entity;
        }
    }
    
    public function findAll($params = array()){

        if(is_scalar($params)){
            $params = array($this->meta->getKey() => $params);
        }
        
        if(is_array($params)){
            $select = $this->dbContext->select('*', $this->meta->getEntityName());
            
            foreach($params as $key=>$param){
                $select->where($key.'=:'.$key);
            }
        
            $entityCollection = $select->toList($this->meta->getEntityName(), $params);
            
            foreach($entityCollection as $entity){
                $entityContext = $this->add($entity);
                $entityContext->setState(EntityContext::PERSISTED);
                $this->dbContext->getPersistedEntities()->add($entityContext->getHashCode(), $entityContext);
            }
            
            return $entityCollection;
        }
    }
    
    public function add($entity){
        $entityContext = new EntityContext($entity);
        $this->entities[] = $entityContext;
        return $entityContext;
    }
    
    public function getEntities(){
        return $this->entities;
    }
    
    public function getMeta(){
        return $this->meta;
    }
}

?>