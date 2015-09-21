<?php

namespace System\Data\Entity;

class SqlQuery {
    
    protected $conn;
    protected $metaReader;
    protected $sql;
    protected $params;
    
    public function __construct(\System\Data\Database $conn, \System\Data\Entity\MetaReaders\IMetaReader $metaReader, $sql = null, $params = null){
        $this->conn = $conn;
        $this->metaReader = $metaReader;
        $this->sql = $sql;
        $this->params = $params;
    }
    
    public function setQuery($sql, $param = array()){
        $this->sql = $sql;
        $this->params = $param;
        return $this;
    }
    
    public function column(){
        $stm = $this->conn->query($this->sql, $this->params);
        return $stm->fetchColumn();
    }
    
    public function single($entityName = null, $default = false){
        $stm = $this->conn->query($this->sql, $this->params);

        if($entityName){
            $row = $stm->fetch(\PDO::FETCH_OBJ);
            return $this->toEntity($row, $entityName, $default);
        }
        
        return $stm->fetch(\PDO::FETCH_OBJ);
    }
    
    public function toList($entityName = null){
        $stm = $this->conn->query($this->sql, $this->params);
        
        if($entityName){
            $array = array();
            $rows = $stm->fetchAll(\PDO::FETCH_OBJ);
            foreach($rows as $row){
                $array[] = $this->toEntity($row, $entityName);
            }
            return new DbListResult($array);
        }else{
            $rows = $stm->fetchAll(\PDO::FETCH_OBJ);
        }
        return new DbListResult($rows);
    }
    
    public function nonQuery(){
        $stm = $this->conn->query($this->sql, $this->params);
        return $stm->rowCount();
    }
    
    public function getMetaReader(){
        return $this->metaReader;
    }
    
    private function toEntity($data, $entityName, $default = false){

        if(is_callable($entityName)){
            return $entityName($data);
        }
        
        if(is_string($entityName)){
            $class = '\\'.str_replace('.', '\\', $entityName);
            $refClass = new \ReflectionClass($class);
            $entity = $refClass->newInstance();

            if(!$data){
                if($default){ 
                    return $entity;
                }
                return false;
            }
            
            $data = get_object_vars($data);

            if(is_array($data)){
                $properties = $refClass->getProperties();

                foreach($properties as $property){
                    $property->setAccessible(true);
                    $propertyName = $property->getName();

                    if(substr($propertyName, 0,1) != '_'){
                        if(array_key_exists($propertyName, $data)){ 
                            $value = $data[$propertyName];
                            $property->setValue($entity, $value);
                        }else{
                            throw new \System\Data\Entity\EntityException(sprintf("The entity property '%s.%s' could not be mapped to the database result.", $entityName, $propertyName));
                        }
                    }
                }
            }
            return $entity;
        }
    }
}