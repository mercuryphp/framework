<?php

namespace System\Web\Mvc;

class ViewBag extends \System\Collections\Dictionary {

    public function toArray(){
        $coll = $this->escape($this->collection);
        return $coll;
    }
    
    protected function escape($collection){
        foreach($collection as $key=>$item){
            if(is_string($item)){
                $collection[$key] = htmlspecialchars($item);
            }elseif(is_object($item)){
                $refClass = new \ReflectionObject($item);
                $properties = $refClass->getProperties();
                $array = array();
                
                foreach($properties as $property){
                    $property->setAccessible(true);
                    $array[$property->getName()] = $property->getValue($item);
                }
                $collection[$key] = \System\Std\Instance::setProperties($item, $this->escape($array));

            }elseif(is_array($item)){
                $collection[$key] = $this->escape($item);
            }
        }
        return $collection;
    }
}

?>