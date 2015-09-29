<?php

namespace System\Data\Entity\MetaReaders;

use System\Std\Str;
use System\Std\Object;

class AnnotationReader extends MetaReader {
    
    /**
     * Gets an EntityMeta object for the specified $entityName.  
     * 
     * @param   string $entityName
     * @return  System.Data.Entity.EntityMeta
     */
    public function read($entityName){
        $tableName = new Str(Str::set($entityName)->toLower()->split('\.')->last());
        $className = Str::set($entityName)->replace('.', '\\');

        $refClass = new \ReflectionClass((string)$className);
        $tokens = token_get_all(file_get_contents($refClass->getFileName())); 

        $meta = array(
            'System.Data.Entity.Attributes.Table' => new \System\Data\Entity\Attributes\Table((string)$tableName),
            'System.Data.Entity.Attributes.Key' => new \System\Data\Entity\Attributes\Key((string)$tableName->append('_id')),
            'Columns' => array()
        );
        $tmp = array();
        $exitLoop = false;

        foreach($tokens as $token){
            switch($token[0]){
                case T_DOC_COMMENT:
                    $lines = explode(PHP_EOL, $token[1]);
                    
                    foreach($lines as $line){
                        $strAttr = trim(str_replace(array('*', '/'), '', $line));

                        if($strAttr){
                            $attribute = Str::set($strAttr)->get('@', '(');
                            $args = Str::set($strAttr)->get('(', ')');

                            if(!$attribute->indexOf('.')){
                                $attribute = $attribute->prepend('System.Data.Entity.Attributes.');
                            }
                            
                            try{
                                $meta[(string)$attribute] = Object::getInstance($attribute, $this->getArgs($args));
                            }catch(\Exception $e){
                                throw new \System\Data\Entity\EntityException('The attribute "'.(string)$attribute.'" does not exist.');
                            }
                        }
                    }
                    break;
                    
                case T_COMMENT:
                    $attribute = Str::set($token[1])->subString(2)->get('@', '(');
                    $args = Str::set($token[1])->get('(', ')', true);

                    if(!$attribute->indexOf('.')){
                        $attribute = $attribute->prepend('System.Data.Entity.Attributes.');
                    }
                    
                    if((string)$args){
                        $tmp[(string)$attribute] = Object::getInstance($attribute, str_getcsv($args, ','));
                    }else{
                        $tmp[(string)$attribute] = Object::getInstance($attribute);
                    }
                    break;
                
                case T_VARIABLE:
                    $property = Str::set($token[1])->subString(1);
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

        $metaData = new \System\Data\Entity\EntityMeta((string)$entityName, $meta);
        return $metaData;
    }
}