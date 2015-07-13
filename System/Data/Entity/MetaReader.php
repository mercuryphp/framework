<?php

namespace System\Data\Entity;

use System\Std\String;
use System\Std\Object;

class MetaReader {
    public static function getMeta($entityName){
        $class = '\\'.str_replace('.', '\\', $entityName);
        $refClass = new \ReflectionClass($class);

        $tokens = token_get_all(file_get_contents($refClass->getFileName())); 

        $meta = array('Columns' => array());
        $tmp = array();
        $exitLoop = false;

        foreach($tokens as $token){
            switch($token[0]){
                case T_DOC_COMMENT:
                    $lines = explode(PHP_EOL, $token[1]);
                    
                    foreach($lines as $line){
                        $strAttr = trim(str_replace(array('*', '/'), '', $line));

                        if($strAttr){
                            $attribute = String::set($strAttr)->get('@', '(');
                            $args = String::set($strAttr)->get('(', ')');

                            if(!$attribute->indexOf('.')){
                                $attribute = $attribute->prepend('System.Data.Entity.Annotations.');
                            }

                            $meta[$attribute->split('.')->last()] = Object::getInstance($attribute, str_getcsv($args, ','));
                        }
                    }
                    break;
                    
                case T_COMMENT:
                    $attribute = String::set($token[1])->subString(2)->get('@', '(');
                    $args = String::set($token[1])->get('(', ')', true);

                    if(!$attribute->indexOf('.')){
                        $attribute = $attribute->prepend('System.Data.Entity.Annotations.');
                    }
                    
                    if((string)$args){
                        $tmp[] = Object::getInstance($attribute, str_getcsv($args, ','));
                    }else{
                        $tmp[] = Object::getInstance($attribute);
                    }
                    break;
                
                case T_VARIABLE:
                    $property = String::set($token[1])->subString(1);
                    $meta['Columns'][(string)$property] = $tmp;
                    $tmp = array();
                    break;
                
                case T_FUNCTION:
                    $exitLoop = true;
                    break;
            }
            
            if($exitLoop){
                break;
            }
        }

        $metaData = new EntityMeta($entityName, $meta);
        return $metaData;
    }
}

?>