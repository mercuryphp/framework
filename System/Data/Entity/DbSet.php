<?php

namespace System\Data\Entity;

class DbSet {
    
    protected $dbContext;
    protected $meta;
    protected $entities = array();
    
    /**
     * Initializes an instance of DbSet.
     * 
     * @param   System.Data.Entity.DbContext $dbContext
     * @param   System.Data.Entity.EntityMeta $meta
     */
    public function __construct(DbContext $dbContext, EntityMeta $meta){
        $this->dbContext = $dbContext;
        $this->meta = $meta;
    }
    
    public function query($sql, $params = array()){
        $sqlQuery = new SqlQuery($this->dbContext->getDatabase(), $this->dbContext->getMetaCollection());
        $sqlQuery->setQuery($sql, $params);
        return $sqlQuery;
    }

    /**
     * Gets a new QueryBuilder instance that has been initilazied to select 
     * from the database table represented by the entity type for this DbSet.
     * 
     * @param   string $fields = '*'
     * @return  System.Data.Entity.QueryBuilder
     */
    public function select($fields = '*'){
        $queryBuilder = new QueryBuilder(new SqlQuery($this->dbContext->getDatabase(), $this->dbContext->getMetaCollection()), $this->meta->getEntityName(), QueryBuilder::SELECT);
        $queryBuilder->setFields($fields);
        return $queryBuilder;
    }
    
    public function insert(){
        $queryBuilder = new QueryBuilder(new SqlQuery($this->dbContext->getDatabase(), $this->dbContext->getMetaCollection()), $this->meta->getEntityName(), QueryBuilder::INSERT);
        return $queryBuilder;
    }
    
    public function update(){
        $queryBuilder = new QueryBuilder(new SqlQuery($this->dbContext->getDatabase(), $this->dbContext->getMetaCollection()), $this->meta->getEntityName(), QueryBuilder::UPDATE);
        return $queryBuilder;
    }
    
    public function delete(){
        $queryBuilder = new QueryBuilder(new SqlQuery($this->dbContext->getDatabase(), $this->dbContext->getMetaCollection()), $this->meta->getEntityName(), QueryBuilder::DELETE);
        return $queryBuilder;
    }
    
    /**
     * Finds an entity using the specified $params. If the entity is found, it is
     * attached to the context. Returns false if the entity is not found. 
     * The optional $default argument determines if a default entity should be 
     * returned with empty property values.
     * 
     * @param   mixed $params
     * @param   bool $default = false
     * @return  mixed
     */
    public function find($params, $default = false, $orderBy = ''){

        if(is_scalar($params)){
            $params = array($this->meta->getKey()->getKeyName() => $params); 
        }
        
        if(is_object($params)){
            $entityName = \System\Std\Str::set(get_class($params))->trim('\\')->replace('\\', '.')->toString();
            $entityMeta = $this->dbContext->getMetaCollection()->get($entityName);
            $params = array($entityMeta->getKey()->getKeyName() => \System\Std\Object::getPropertyValue($params, $entityMeta->getKey()->getKeyName()));
        }
        
        if(is_array($params)){
            $select = $this->select('*', $this->meta->getEntityName());
            
            foreach($params as $key=>$param){
                $field = ':'.$key;
                if(substr($param, 0,1) == '@'){
                    $param = substr($param,1);
                    if($this->dbContext->getDatabase()->queryParams()->hasKey($param)){
                        $field = '@'.$param;
                    }
                }
                $select->where($key.'='.$field);
            }

            if($orderBy){
                $select->orderBy($orderBy);
            }
            
            $entity = $select->single($params, $this->meta->getEntityName(), $default);
            
            if($entity){
                $entityContext = $this->add($entity);
                $entityContext->setState(EntityContext::PERSISTED);
                $this->dbContext->getEntries()->add($entityContext->getHashCode(), $entityContext);

                return $entity;
            }
        }
        return false;
    }
    
    /**
     * Finds all entities using the specified $params. If the entities are 
     * found, they are attached to the context.
     * 
     * @param   mixed $params
     * @return  System.Data.Entity.DbListResult
     */
    public function findAll($params = array(), $orderBy = ''){

        if(is_scalar($params)){
            $params = array($this->meta->getKey()->getKeyName() => $params);
        }
        
        if(is_object($params)){
            $entityName = \System\Std\Str::set(get_class($params))->trim('\\')->replace('\\', '.')->toString();
            $entityMeta = $this->dbContext->getMetaCollection()->get($entityName);
            $params = array($entityMeta->getKey()->getKeyName() => \System\Std\Object::getPropertyValue($params, $entityMeta->getKey()->getKeyName()));
        }

        if(is_array($params)){
            $select = $this->select('*', $this->meta->getEntityName());
            
            foreach($params as $key=>$param){
                $field = ':'.$key;
                if(substr($param, 0,1) == '@'){
                    $param = substr($param,1);
                    if($this->dbContext->getDatabase()->queryParams()->hasKey($param)){
                        $field = '@'.$param;
                    }
                }
                $select->where($key.'='.$field);
            }
            
            if($orderBy){
                $select->orderBy($orderBy);
            }

            $entityCollection = $select->toList($params, $this->meta->getEntityName());
            
            foreach($entityCollection as $entity){
                $entityContext = $this->add($entity);
                $entityContext->setState(EntityContext::PERSISTED);
                $this->dbContext->getEntries()->add($entityContext->getHashCode(), $entityContext);
            }
            
            return $entityCollection;
        }
    }

    public function findColumn($params, $columnName){
        
        $select = $this->select($columnName, $this->meta->getEntityName());
        
        foreach($params as $key=>$param){
            $select->where($key.'=:'.$key);
        }
        return $select->column($params, $columnName);
    }
    
    /**
     * Adds an entity to the DbSet by creating a new EntityContext
     * object. The EntityContext object is then returned.
     * 
     * @param   mixed $entity
     * @return  System.Data.Entity.EntityContext
     */
    public function add($entity){
        if(is_object($entity)){
            $entityContext = new EntityContext($entity);
            $this->entities[$entityContext->getHashCode()] = $entityContext;
            return $entityContext;
        }
    }
    
    /**
     * Adds a collection of entities to the DbSet by creating a new 
     * EntityContext for each entity.
     * 
     * @param   mixed $collection
     * @return  void
     */
    public function addRange($collection){
        if($collection instanceof \Traversable || is_array($collection)){
            foreach($collection as $entity){
                $entityContext = new EntityContext($entity);
                $this->entities[$entityContext->getHashCode()] = $entityContext;
            }
        }else{
            throw new \Exception('The supplied argument is not traversable.');
        }
    }
    
    /**
     * Changes the state of an entity such that when saveChanges() is called, the
     * entity will be deleted from the data store.
     * 
     * @param   mixed $entity
     * @return  void
     */
    public function remove($entity){
        if(is_object($entity)){
            $objHash = spl_object_hash($entity);
            if(array_key_exists($objHash, $this->entities)){
                $this->entities[$objHash]->setState(EntityContext::DELETE);
            }
        }
    }
    
    /**
     * Removes an entity from the DbSet collection.
     * 
     * @param   mixed $entity
     * @return  bool
     */
    public function detach($entity){
        if(is_object($entity)){
            $objHash = spl_object_hash($entity);
            unset($this->entities[$objHash]);
            return true;
        }
        return false;
    }
    
    /**
     * Gets the underlying array that holds the entities.
     * 
     * @return  array
     */
    public function getEntities(){
        return $this->entities;
    }
    
    /**
     * Gets the EntityMeta object for the DbSet.
     * 
     * @return  System.Data.Entity.EntityMeta
     */
    public function getMeta(){
        return $this->meta;
    }
}