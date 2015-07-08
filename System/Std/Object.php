<?php

namespace System\Std;

final class Object{
    
    public static function cast($fromObj, $toObj){
        
        $refClass1 = new \ReflectionClass($fromObj);
        
        $className = '\\'.str_replace('.', '\\', $toObj);
        $refClass2 = new \ReflectionClass($className);
        $toObjInstance = $refClass2->newInstance();
        
        $fromObjProperties = $refClass1->getProperties();
        
        foreach($fromObjProperties as $property){
            $property->setAccessible(true);
            $propertyName = $property->getName();
            $propertyValue = $property->getValue($fromObj);
            
            if($refClass2->hasProperty($propertyName)){
                $toProperty = $refClass2->getProperty($propertyName);
                $toProperty->setAccessible(true);
                $toProperty->setValue($toObjInstance, $propertyValue);
            }
        }
        return $toObjInstance;
    }
    
    public static function toObject(){
        $args = func_get_args();
        
        if(count($args) >=2){

            $className = '\\'.str_replace('.', '\\', $args[0]);
            $refClass = new \ReflectionClass($className);
            $toObjInstance = $refClass->newInstance();

            unset($args[0]);
            
            foreach($args as $arg){
                if(is_array($arg)){
                    foreach($arg as $propertyName=>$propertyValue){
                        if($refClass->hasProperty($propertyName)){
                            $property = $refClass->getProperty($propertyName);
                            $property->setAccessible(true);
                            $property->setValue($toObjInstance, $propertyValue);
                        }
                    }
                }
            }
            
            return $toObjInstance;
        }
    }
    
    public static function setProperties($object, array $properties){
        $refClass = new \ReflectionObject($object);
        
        foreach($properties as $key=>$value){
            $property = $refClass->getProperty($key);
            $property->setAccessible(true);
            $property->setValue($object, $value);
        }
        return $object;
    }
    
    public static function getInstance($name, array $args = array()){
        $name = str_replace(".", "\\", $name);
        $refClass = new \ReflectionClass($name);
        return $refClass->newInstanceArgs($args);
    }
}

?>