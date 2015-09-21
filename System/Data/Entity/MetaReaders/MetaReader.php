<?php

namespace System\Data\Entity\MetaReaders;

abstract class MetaReader {
    
    public abstract function read($entityName);
    
    protected function getArgs($args){
        $list = str_getcsv($args, ",", "'");
        $argList = array();
        foreach($list as $item){
            if(substr($item,0,1) == '[' && substr($item,-1) == ']'){
                $argList[] = array_map('trim', str_getcsv(substr($item,1,-1), ",", "'"));
            }else{
                $argList[] = $item;
            }
        }
        return $argList;
    }
}