<?php

namespace System\Data\Entity;

class QueryBuilder {
    
    protected $sqlQuery;
    protected $selectFields;
    protected $sql;
    protected $params;
    protected $lastTableAlias;
    protected $isWhere = false;
    protected $groupOrWhere  = array();
    protected $entityKeys = array();
    
    const SELECT = 'SELECT';
    const INSERT = 'INSERT INTO';
    const UPDATE = 'UPDATE';
    const DELETE = 'DELETE';
    
    /**
     * Initializes an instance of QueryBuilder with an instance of
     * System.Data.Entity.SqlQuery, query select fields and the base entity name.
     * 
     * @param   System.Data.Entity.SqlQuery $sqlQuery
     * @param   string $entityName
     * @param   string $queryType
     */
    public function __construct(SqlQuery $sqlQuery, $entityName, $queryType){
        
        $this->sqlQuery = $sqlQuery;
        $this->params = new \System\Collections\Dictionary();
        $metaData = $this->sqlQuery->getMetaCollection()->get($entityName);
        $tableAlias = $this->getTableNameAlias($metaData->getTable()->getTableName());
        $this->lastTableAlias = $tableAlias;
        $this->entityKeys[] = $metaData->getKey()->getKeyName();
        
        $this->sql = \System\Std\Str::set($queryType.' ');
        if ($queryType == QueryBuilder::SELECT || $queryType == QueryBuilder::DELETE){
            $this->sql = $this->sql->append('FROM ');
        }
        
        $this->sql = $this->sql->append($metaData->getTable()->getTableName().' ');

        if ($queryType == QueryBuilder::SELECT){
            $this->sql = $this->sql->append($tableAlias);
        }
            
        $this->sql = $this->sql->appendLine('');
    }
    
    public function select($fields){ 
        $this->selectFields = $fields;
        return $this;
    }
    
    public function setFields($fields){ 
        $this->selectFields = $fields;
        $this->sql = $this->sql->insert('@fields'.' ', 7);
        return $this;
    }
    
    public function addParam($name, $value){
        $this->params->add($name, $value);
    }

    /**
     * Adds a JOIN clause to the underlying SQL query using the 
     * specified $entityName. If $join is specified, it is used as the joining 
     * condition.
     * 
     * @param   string $entityName
     * @param   string $join
     * @return  System.Data.Entity.QueryBuilder
     */
    public function join($entityName, $join = null){
        return $this->_join('INNER JOIN', $entityName, $join);
    }

    /**
     * Adds a LEFT JOIN clause to the underlying SQL query using the 
     * specified $entityName. If $join is specified, it is used as the joining 
     * condition.
     * 
     * @param   string $entityName
     * @param   string $join
     * @return  System.Data.Entity.QueryBuilder
     */
    public function leftJoin($entityName, $join = null){
        return $this->_join('LEFT JOIN', $entityName, $join);
    }

    /**
     * Adds a RIGHT JOIN clause to the underlying SQL query using the 
     * specified $entityName. If $join is specified, it is used as the joining 
     * condition.
     * 
     * @param   string $entityName
     * @param   string $join
     * @return  System.Data.Entity.QueryBuilder
     */
    public function rightJoin($entityName, $join = null){
        return $this->_join('RIGHT JOIN', $entityName, $join);
    }

    /**
     * Adds a WHERE clause to the underlying SQL query using the 
     * specified $condition. Subsequent calls to this method will result in an
     * AND operation.
     * 
     * @param   string $condition
     * @return  System.Data.Entity.QueryBuilder
     */
    public function where($condition, $keyValParam = []){
        $op = "AND";
        if(!$this->isWhere){
            $op = "WHERE";
            $this->isWhere = true;
        }
        $this->params->merge($keyValParam);
        $this->sql = $this->sql->append("$op ")->append($condition.' '.PHP_EOL);
        return $this;
    }

    /**
     * Adds a WHERE clause to the underlying SQL query using the 
     * specified $condition. Subsequent calls to this method will result in an
     * OR operation.
     * 
     * @param   string $condition
     * @return  System.Data.Entity.QueryBuilder
     */
    public function orWhere($condition){
        $op = "OR";
        if(!$this->isWhere){
            $op = "WHERE";
            $this->isWhere = true;
        }
        $this->sql = $this->sql->append("$op ")->append($condition.' '.PHP_EOL);
        return $this;
    }
    
    public function groupOrWhere($prefix, $field, $conditions = array()){
        if(count($conditions) > 0){
            $sql = "";
            foreach($conditions as $idx=>$condition){
                $fieldName = str_replace(".", "_",$field).$idx;
                $sql.= $field.'=:'.$fieldName.' OR ';
                $this->params[$fieldName] = $condition;
            }
            $this->sql = $this->sql->append("$prefix (" .rtrim($sql, ' OR ').")")->append(PHP_EOL);
        }
        return $this;
    }
    
    /**
     * Adds a IN clause to the underlying SQL query using the 
     * specified $fieldName and $values. Subsequent calls to this method will 
     * result in an AND operation.
     * 
     * @param   string $fieldName
     * @param   mixed $values
     * @return  System.Data.Entity.QueryBuilder
     */
    public function whereIn($fieldName, $values){
        $op = "AND";
        if(!$this->isWhere){
            $op = "WHERE";
            $this->isWhere = true;
        }
        if(is_array($values)){
            $values = join(',', $values);
        }
        $this->sql = $this->sql->append("$op ")->append($fieldName.' IN ('.$values.')'.PHP_EOL);
        return $this;
    }
    
    /**
     * Adds a NOT IN clause to the underlying SQL query using the 
     * specified $fieldName and $values. Subsequent calls to this method will 
     * result in an AND operation.
     * 
     * @param   string $fieldName
     * @param   mixed $values
     * @return  System.Data.Entity.QueryBuilder
     */
    public function whereNotIn($fieldName, $values){
        $op = "AND";
        if(!$this->isWhere){
            $op = "WHERE";
            $this->isWhere = true;
        }
        if(is_array($values)){
            $values = join(',', $values);
        }
        $this->sql = $this->sql->append("$op ")->append($fieldName.' NOT IN ('.$values.')'.PHP_EOL);
        return $this;
    }

    /**
     * Adds a GROUP BY clause to the underlying SQL query using the 
     * specified $groupBy.
     * 
     * @param   string $groupBy
     * @return  System.Data.Entity.QueryBuilder
     */
    public function groupBy($groupBy){
        $this->sql = $this->sql->append("GROUP BY ")->append($groupBy.' '.PHP_EOL);
        return $this;
    }

    /**
     * Adds a ORDER BY clause to the underlying SQL query using the 
     * specified $orderBy.
     * 
     * @param   string $orderBy
     * @return  System.Data.Entity.QueryBuilder
     */
    public function orderBy($orderBy){
        $this->sql = $this->sql->append("ORDER BY ")->append($orderBy.' '.PHP_EOL);
        return $this;
    }

    /**
     * Adds a raw SQL to the underlying SQL query.
     * 
     * @param   string $sql
     * @return  System.Data.Entity.QueryBuilder
     */
    public function raw($sql){
        $this->sql = $this->sql->append($sql.' '.PHP_EOL);
        return $this;
    }
    
    /**
     * Gets the underlying SQL statement as a string.
     * 
     * @return  string
     */
    public function sql(){
        return $this->sql->replace('@fields', $this->selectFields)->toString();
    }
    
    /**
     * 
     * @param   array $params
     * @param   string $columnName
     * @return  mixed
     */
    public function column($params = array(), $columnName = ''){
        return $this->sqlQuery
            ->setQuery($this->sql(), $this->params->merge($params)->toArray())
            ->column($columnName);
    }
    
    /**
     * Gets a single row as an object. If $entityType is specified as a string,
     * then an instance of $entityType is created and returned where all column 
     * names are mapped to the entity's properties. If $default is specified and 
     * $entityType is a string, then gets a default object if no record is found.
     * 
     * @param   array $params
     * @param   mixed $entityType
     * @param   bool $default
     * @return  mixed
     */
    public function single($params = array(), $entityType = null, $default = false){
        return $this->sqlQuery
            ->setQuery($this->sql(), $params)
            ->single($entityType, $default);
    }

    /**
     * Gets a collection of rows as a DbListResult where each row is respresented 
     * as an object. If $entityType is specified as a string, then an instance of 
     * $entityType is created for each row where all column names are mapped to 
     * the entity's properties. $entityType can also be a callback function that 
     * is passed a row set or an instance of System.Data.Entity.Relations.Relationship.
     * 
     * @param   mixed $entityType = null
     * @return  System.Data.Entity.DbListResult
     */
    public function toList($params = array(), $entityType = null){
        return $this->sqlQuery
            ->setQuery($this->sql(), $this->params->merge($params)->toArray())
            ->toList($entityType);
    }
    
    public function nonQuery($params = array()){
        return $this->sqlQuery
            ->setQuery($this->sql(), $params)
            ->nonQuery();
    }
    
    public function getParams(){
        return $this->params;
    }

    protected function getTableNameAlias($tableName){
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

    protected function _join($type, $entityName, $join = null){

        $metaData = $this->sqlQuery->getMetaCollection()->get($entityName);
        $table = $metaData->getTable()->getTableName();

        $this->sql = $this->sql->append($type.' ')->append($table);

        if($join){
            $segments = explode('.', $join, 2);
            $alias = isset($segments[0]) ? $segments[0] : '';
        }else{
            $key = array_pop($this->entityKeys);
            $alias = $this->getTableNameAlias($table);
            $join = $alias.'.'.$key.' = '.$this->lastTableAlias.'.'.$key;
        }
        
        $this->sql = $this->sql
            ->append(" $alias ")
            ->append('ON ')
            ->append($join.' '.PHP_EOL);
        
        $this->entityKeys[] = $metaData->getKey()->getKeyName();
        $this->lastTableAlias = $alias;
        return $this;
    }
}

