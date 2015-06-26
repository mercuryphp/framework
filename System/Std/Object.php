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
}

?>