<?php

namespace System\Data\Graphs;

abstract class NodeList {
    
    protected $nodeList = array();
    protected $nodeLevel;

    /**
     * Gets a boolean value that determines if there are any child nodes.
     * 
     * @return  bool
     */
    public function hasNodes(){
        return (count($this->nodeList) > 0) ? true : false;
    }
    
    /**
     * Gets a count of child nodes.
     * 
     * @return  int
     */
    public function count(){
        return count($this->nodeList);
    }
    
    /**
     * Gets the node level.
     * 
     * @return  int
     */
    public function getNodeLevel(){
        return $this->nodeLevel;
    }

    /**
     * Gets an array of child nodes where each element in the array is of 
     * type ObjectNodeList. If $index is specified, then gets an ObjectNodeList
     * using the index from the array. Returns false if the indexed 
     * ObjectNodeList does not exist.
     * 
     * @param   int $index
     * @return  mixed
     */
    public function getChildNodes($index = null){
        if(!is_null($index)){
            if(array_key_exists($index, $this->nodeList)){
                return $this->nodeList[$index];
            }
            return false;
        }
        return $this->nodeList;
    }
    
    /**
     * Gets the first child node.
     * 
     * @return  System.Data.Graphs.ObjectNodeList
     */
    public function getFirstChildNode(){
        return reset($this->nodeList);
    }
    
    /**
     * Gets the last child node.
     * 
     * @return  System.Data.Graphs.ObjectNodeList
     */
    public function getLastChildNode(){
        return end($this->nodeList);
    }
}
