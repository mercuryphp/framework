<?php

namespace System\Std;

final class Obj{

    /**
     * A variadic method that converts key/value arrays or objects to an object
     * specified by $toClass name.
     * 
     * @param   string $toClass
     * @param   array|object $data1, $data2...
     * @return  object
     */
    public static function toObject(){
        $args = func_get_args();
        
        if(count($args) >=2){
            $className = '\\'.str_replace('.', '\\', $args[0]);
            $refClass = new \ReflectionClass($className);
            $toObjInstance = $refClass->newInstance();

            unset($args[0]);
            
            foreach($args as $arg){
                
                if(is_object($arg)){
                    $arg = Obj::getProperties($arg);
                }
                
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
    
    /**
     * Sets the property values of an object using an array.
     * 
     * @param   object $object
     * @param   array $properties
     * @return  object
     */
    public static function setProperties($object, array $properties){
        
        if(!is_object($object)){
            throw new \RuntimeException(sprintf('Obj::getProperties() expects parameter 1 to be object, %s given', gettype($object)));
        }
        
        $refClass = new \ReflectionObject($object);

        foreach($properties as $key=>$value){
            $property = $refClass->getProperty($key);
            $property->setAccessible(true);
            $property->setValue($object, $value);
        }
        return $object;
    }
    
    /**
     * Gets the properties of an object as an array.
     * 
     * @param   object $object
     * @param   mixed $filter = null
     * @return  array
     */
    public static function getProperties($object, $filter = null){

        if(!is_object($object)){
            throw new \RuntimeException(sprintf('Obj::getProperties() expects parameter 1 to be object, %s given', gettype($object)));
        }
        
        if(is_null($filter)){
            $filter = \ReflectionProperty::IS_PUBLIC | 
                \ReflectionProperty::IS_PROTECTED | 
                \ReflectionProperty::IS_PRIVATE |
                \ReflectionProperty::IS_STATIC;
        }
        
        $refClass = new \ReflectionObject($object);
        $properties = $refClass->getProperties($filter);
        $array = array();

        foreach($properties as $property){
            $property->setAccessible(true);
            $array[$property->getName()] = $property->getValue($object);
        }
        return $array;
    }
    
    /**
     * Sets the property value of an object.
     * 
     * @param   object $object
     * @param   string $propertyName
     * @param   mixed $value
     * @return  void
     */
    public static function setPropertyValue($object, $propertyName, $value){
        
        if(!is_object($object)){
            throw new \RuntimeException(sprintf('Obj::setPropertyValue() expects parameter 1 to be object, %s given', gettype($object)));
        }
        
        $refClass = new \ReflectionObject($object);

        if($refClass->hasProperty($propertyName)){
            $property = $refClass->getProperty($propertyName);
            $property->setAccessible(true);
            $property->setValue($object, $value);
        }
    }
   
    /**
     * Gets the property value of an object.
     * 
     * @param   object $object
     * @param   string $propertyName
     * @return  mixed
     */
    public static function getPropertyValue($object, $propertyName){
        
        if(!is_object($object)){
            throw new \RuntimeException(sprintf('Obj::getPropertyValue() expects parameter 1 to be object, %s given', gettype($object)));
        }
        
        $refClass = new \ReflectionObject($object);
        if($refClass->hasProperty($propertyName)){
            $property = $refClass->getProperty($propertyName);
            $property->setAccessible(true);
            return $property->getValue($object);
        }
    }
    
    public static function getMethodAnnotations($object, $methodName){
        
        if(!is_object($object)){
            throw new \RuntimeException(sprintf('Obj::getMethodAnnotations() expects parameter 1 to be object, %s given', gettype($object)));
        }
        
        $refClass = new \ReflectionObject($object);
        $tokens = token_get_all(file_get_contents($refClass->getFileName()));

        $comments = array();
        foreach($tokens as $idx=>$token){

            if(isset($token[1]) && $token[0] == T_COMMENT){
                $comments[$token[2]] = $token[1];
            }
            
            if(isset($token[1]) && ($token[1] == $methodName) && ($tokens[$idx -2][0] == T_FUNCTION)){
                $keys = array_reverse(array_keys($comments));
                foreach ($keys as $i => $key) { 
                    if($token[2] - ($i+$key+1) == 0){
                        $attribute = Str::set($comments[$key])->get('@', '(');

                        if((string)$attribute){
                            $args = (string)Str::set($comments[$key])->get('(', ')');
                            $args = $args ? str_getcsv($args, ',', '"') : array();
                            $comments[$key] = Obj::getInstance((string)$attribute->append('Attribute'), array_map('trim', $args));
                        }else{
                            unset($comments[$key]);
                        }
                    }else{
                        unset($comments[$key]);
                    }
                }
            }
        }
        return $comments;
    }
    
    /**
     * Gets a new instance of a class.
     * 
     * @method  getInstance
     * @param   string $name
     * @param   array $args
     * @param   bool $throwException = true
     * @return  object
     */
    public static function getInstance($name, array $args = array(), $throwException = true){
        try{
            $name = str_replace(".", "\\", $name);
            $refClass = new \ReflectionClass($name);

            if(count($args) > 0){
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
    
    public static function hasProperty($object, $property){
        $obj = new \ReflectionObject($object);
        return $obj->hasProperty($property);
    }
}