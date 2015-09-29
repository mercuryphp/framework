<?php

namespace System\Data\Graphs;

class ObjectNodeList extends NodeList {
    
    protected $rootPath;
    protected $data;

    /**
     * Initializes an instance of ObjectNodeList with a node path, the node data
     * and an array of child nodes.
     * 
     * @param   string $rootPath
     * @param   mixed $data
     * @param   array $list
     */
    public function __construct($rootPath, $data, array $list){
        $this->rootPath = $rootPath;
        $this->data = $data;
        $count = count(explode(':', $rootPath)) + 1;
        
        foreach($list as $path => $data){
            $nodes = explode(':', $path);
            $len = strlen($rootPath); 

            if((substr($path,0, $len) == $rootPath) && ($count == count($nodes))){
                unset($list[$path]);
                $this->nodeList[] = $nodeList = new ObjectNodeList($path, $data, $list);
            }
        }
        $this->nodeLevel = $count-1;
        unset($list);
    }
    
    /**
     * Gets the path of the current ObjectNodeList instance.
     * @return  mixed
     */
    public function getPath(){
        return $this->rootPath;
    }
    
    /**
     * Gets the data for the current node. If $entityName is specified, then the
     * data is converted to the type and returned.
     * 
     * @param   string $entityName
     * @return  mixed
     */
    public function get($entityName = ''){
        if($entityName){
            return \System\Std\Object::toObject($entityName, $this->data);
        }
        return $this->data;
    }
}