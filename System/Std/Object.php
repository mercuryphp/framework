<?php

namespace System\Std;

final class Object{

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
    
    public static function getInstance($name, $args = null, $throwException = true){
        try{
            $name = str_replace(".", "\\", $name);
            $refClass = new \ReflectionClass($name);

            if($args){
                $instance = $refClass->newInstanceArgs($args);
            }else{
                $instance = $refClass->newInstance();
            }
        }catch(\ReflectionException $rex){
            if($throwException){
                throw $rex;
            }
            return null;
        }
        return $instance;
    }
}