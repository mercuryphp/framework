<?php

namespace System\Data\Entity;

class SelectQuery {
    
    protected $sqlQuery;
    protected $fields;
    protected $sql;
    protected $lastTableAlias;
    protected $isWhere = false;
    
    /**
     * Initializes an instance of SelectQuery with an instance of
     * System.Data.Entity.SqlQuery, query select fields and the base entity name.
     * 
     * @param   System.Data.Entity.SqlQuery $sqlQuery
     * @param   string $fields
     * @param   string $entityName
     */
    public function __construct(SqlQuery $sqlQuery, $fields, $entityName){
        
        $this->sqlQuery = $sqlQuery;
        $this->fields = $fields;

        $metaData = $this->sqlQuery->getMetaCollection()->get($entityName);
        $tableAlias = $this->getTableNameAlias($metaData->getTable()->getTableName());
        $this->lastTableAlias = $tableAlias;
        
        $this->sql = \System\Std\Str::set('SELECT ')
            ->append($fields)
            ->append(' FROM ')
            ->append($metaData->getTable()->getTableName())
            ->append(' '.$tableAlias)
            ->append(PHP_EOL);
    }
    
    /**
     * Adds a JOIN clause to the underlying SQL query using the 
     * specified $entityName. If $join is specified, it is used as the joining 
     * condition.
     * 
     * @param   string $entityName
     * @param   string $join
     * @return  System.Data.Entity.SelectQuery
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
     * @return  System.Data.Entity.SelectQuery
     */
    public function left($entityName, $join = null){
        return $this->_join('LEFT JOIN', $entityName, $join);
    }
    
    /**
     * Adds a RIGHT JOIN clause to the underlying SQL query using the 
     * specified $entityName. If $join is specified, it is used as the joining 
     * condition.
     * 
     * @param   string $entityName
     * @param   string $join
     * @return  System.Data.Entity.SelectQuery
     */
    public function right($entityName, $join = null){
        return $this->_join('RIGHT JOIN', $entityName, $join);
    }
    
    /**
     * Adds a WHERE clause to the underlying SQL query using the 
     * specified $condition. Subsequent calls to this method will result in an
     * AND operation.
     * 
     * @param   string $condition
     * @return  System.Data.Entity.SelectQuery
     */
    public function where($condition){
        $op = "AND";
        if(!$this->isWhere){
            $op = "WHERE";
            $this->isWhere = true;
        }
        $this->sql = $this->sql->append("$op ")->append($condition.' '.PHP_EOL);
        return $this;
    }
    
    /**
     * Adds a WHERE clause to the underlying SQL query using the 
     * specified $condition. Subsequent calls to this method will result in an
     * OR operation.
     * 
     * @param   string $condition
     * @return  System.Data.Entity.SelectQuery
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
    
    /**
     * Adds a GROUP BY clause to the underlying SQL query using the 
     * specified $groupBy.
     * 
     * @param   string $groupBy
     * @return  System.Data.Entity.SelectQuery
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
     * @return  System.Data.Entity.SelectQuery
     */
    public function orderBy($orderBy){
        $this->sql = $this->sql->append("ORDER BY ")->append($orderBy.' '.PHP_EOL);
        return $this;
    }
    
    /**
     * Adds a raw SQL to the underlying SQL query.
     * 
     * @param   string $sql
     * @return  System.Data.Entity.SelectQuery
     */
    public function raw($sql){
        $this->sql = $this->sql->append($sql.' '.PHP_EOL);
        return $this;
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
            ->setQuery($this->sql->toString(), $params)
            ->single($entityType, $default);
    }

    /**
     * Gets a collection of rows as a DbListResult where each row is respresented 
     * as an object. If $entityType is specified as a string, then an instance of $entityType 
     * is created for each row where all column names are mapped to the entity's properties.
     * 
     * @param   mixed $entityType = null
     * @return  System.Data.Entity.DbListResult
     */
    public function toList($params = array(), $entityType = null){
        return $this->sqlQuery
            ->setQuery($this->sql->toString(), $params)
            ->toList($entityType);
    }

    /**
     * Gets the underlying SQL statement as a string.
     * 
     * @return  string
     */
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
        
        $metaData = $this->sqlQuery->getMetaCollection()->get($entityName);
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
        
        $this->lastTableAlias = $alias;
        return $this;
    }
}