<?php

namespace System\Data\Entity;

class SelectQuery {
    
    protected $sqlQuery;
    protected $fields;
    protected $metaCollection = array();
    protected $sql;
    protected $lastTableAlias;
    protected $isWhere = false;
    
    public function __construct(SqlQuery $sqlQuery, $fields, $entityName){
        
        $this->sqlQuery = $sqlQuery;
        $this->fields = $fields;

        if(array_key_exists($entityName, $this->metaCollection)){
            $metaData = $this->metaCollection[$entityName];
        }else{
            $metaData = MetaReader::getMeta($entityName);
            $this->metaCollection[$entityName] = $metaData;
        }

        $tableAlias = $this->getTableNameAlias($metaData->getTable()->getTableName());
        $this->lastTableAlias = $tableAlias;
        
        $this->sql = \System\Std\Str::set('SELECT ')
            ->append($fields)
            ->append(' FROM ')
            ->append($metaData->getTable()->getTableName())
            ->append(' '.$tableAlias)
            ->append(PHP_EOL);
    }
    
    public function join($entityName, $join = null){
        return $this->_join('INNER JOIN', $entityName, $join);
    }
    
    public function left($entityName, $join = null){
        return $this->_join('LEFT JOIN', $entityName, $join);
    }
    
    public function right($entityName, $join = null){
        return $this->_join('RIGHT JOIN', $entityName, $join);
    }
    
    public function where($condition){
        $op = "AND";
        if(!$this->isWhere){
            $op = "WHERE";
            $this->isWhere = true;
        }
        $this->sql = $this->sql->append("$op ")->append($condition.' '.PHP_EOL);
        return $this;
    }
    
    public function orWhere($condition){
        $op = "OR";
        if(!$this->isWhere){
            $op = "WHERE";
            $this->isWhere = true;
        }
        $this->sql = $this->sql->append("$op ")->append($condition.' '.PHP_EOL);
        return $this;
    }
    
    public function groupBy($groupBy){
        $this->sql = $this->sql->append("GROUP BY ")->append($groupBy.' '.PHP_EOL);
        return $this;
    }
    
    public function raw($sql){
        $this->sql = $this->sql->append($sql.' '.PHP_EOL);
        return $this;
    }
    
    public function single($params = array(), $entityName = '', $default = false){
        return $this->sqlQuery
            ->setQuery($this->sql->toString(), $params)
            ->single($entityName, $default);
    }

    public function toList($params = array(), $entityName = ''){
        return $this->sqlQuery
            ->setQuery($this->sql->toString(), $params)
            ->toList($entityName);
    }

    public function sql(){
        return $this->sql->toString();
    }

    private function getTableNameAlias($tableName){
        $alias = '';
        
        if(strpos($tableName, '_') > -1){
            $sections = explode('_', $tableName);

            foreach($sections as $section){
                $alias .= $section[0];
            }
        }else{
            $alias = $tableName[0];
        }
        return $alias;
    }
    
    private function _join($type, $entityName, $join = null){
        
        if(array_key_exists($entityName, $this->metaCollection)){
            $metaData = $this->metaCollection[$entityName];
        }else{
            $metaData = MetaReader::getMeta($entityName);
            $this->metaCollection[$entityName] = $metaData;
        }
        
        $table = $metaData->getTable()->getTableName();
        $key = $metaData->getKey()->getKeyName();

        $this->sql = $this->sql->append("$type ")->append($table);

        if($join){
            $segments = explode('.', $join, 2);
            $alias = isset($segments[0]) ? $segments[0] : '';
        }else{
            $alias = $this->getTableNameAlias($table);
            $join = $alias.'.'.$key.' = '.$this->lastTableAlias.'.'.$key;
        }
        
        $this->sql = $this->sql
            ->append(" $alias ")
            ->append('ON ')
            ->append($join.' '.PHP_EOL);
            
        return $this;
    }
}