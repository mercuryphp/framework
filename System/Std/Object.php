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
    
    public static function getProperties($object){
        $refClass = new \ReflectionObject($object);
        $properties = $refClass->getProperties();
        $array = array();
        
        foreach($properties as $property){
            $property->setAccessible(true);
            $array[$property->getName()] = $property->getValue($object);
        }
        return $array;
    }
    
    public static function setPropertyValue($object, $propertyName, $value){
        $refClass = new \ReflectionObject($object);
        
        if($refClass->hasProperty($propertyName)){
            $property = $refClass->getProperty($propertyName);
            $property->setAccessible(true);
            $property->setValue($object, $value);
        }
    }
   
    public static function getPropertyValue($object, $propertyName){
        $refClass = new \ReflectionObject($object);
        if($refClass->hasProperty($propertyName)){
            $property = $refClass->getProperty($propertyName);
            $property->setAccessible(true);
            return $property->getValue($object);
        }
    }
    
    public static function getInstance($name, $args = null){
        $name = str_replace(".", "\\", $name);
        $refClass = new \ReflectionClass($name);

        if($args){
            return $refClass->newInstanceArgs($args);
        }
        return $refClass->newInstance();
    }
}