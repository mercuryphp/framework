<?php

namespace System\Data\Entity;

class MetaReader {
    public static function getMeta($entityName){
        $class = '\\'.str_replace('.', '\\', $entityName);
        $refClass = new \ReflectionClass($class);
        $comments = $refClass->getDocComment();
        $lines = explode(PHP_EOL, $comments);
        
        $meta = array(
            'Table' => '',
            'Key' => '',
            'Columns' => array()
        );

        foreach($lines as $line){ 
            $strAttr = trim(str_replace(array('*', '/'), "", $line));
            if($strAttr){
                $keyVal = explode(':', $strAttr, 2); 
                $array = array_map('trim', $keyVal);

                $key = $array[0];
                $val = $array[1];

                if(array_key_exists($key, $meta)){
                    if($key == 'Columns'){
                        $parts = explode('{', $val, 2); 
                        if(count($parts) == 2){
                            $columnName = trim($parts[0]);

                            $sections = explode(';', trim($parts[1], '} '));

                            foreach($sections as $section){
                                $keyVal = explode('=', $section, 2);
                                $array = array_map('trim', $keyVal);

                                if(count($array) == 2){
                                    $propKey = $array[0];
                                    $propVal = $array[1]; 

                                    $meta['Columns'][$columnName][$propKey] = $propVal;
                                }
                            }
                        }
                    } else{
                        $meta[$key] = $val;
                    }
                }
            }
        }
        
        $metaData = new EntityMeta();
        $metaData->setEntityName($entityName);
        $metaData->setTable($meta['Table']);
        $metaData->setKey($meta['Key']);
        $metaData->setColumns(new EntityColumnMeta($meta['Columns']));
        
        return $metaData;
    }
}

?>