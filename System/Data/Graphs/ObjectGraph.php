<?php

namespace System\Data\Graphs;

class ObjectGraph extends NodeList {
    
    /**
     * Initializes an instance of ObjectGraph with a data set that is traversable.
     * 
     * @param   Traversable $data
     * @param   string $nodeName = 'id'
     * @param   string $parentNodeName = 'parent_id'
     */
    public function __construct($data, $nodeName = 'id', $parentNodeName = 'parent_id'){
        
        if(!$data instanceof \Traversable){
            throw new \InvalidArgumentException('The supplied data is not traversable.');
        }
        
        $array = array();
        foreach($data as $row){
            $found = false;
            $item = \System\Std\Object::getProperties($row);
            $id = $item[$parentNodeName].':'.$item[$nodeName];
            
            foreach($array as $k => $v){
                if(substr($k, -1) == $item[$parentNodeName]){
                    $array[$k.':'.$item[$nodeName]] = $row;
                    $found = true;
                    break;
                }
            }
            if($found == false){
                $array[$id] = $row;
            }
        }

        foreach($array as $path => $data){
            $nodes = explode(':', $path);
            
            if(count($nodes) == 2){
                unset($array[$path]);
                $this->nodeList[] = new ObjectNodeList($path, $data, $array);
            }
        }
        unset($array);
        $this->nodeLevel = 1;
    }
}