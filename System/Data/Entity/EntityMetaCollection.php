<?php

namespace System\Data\Entity;

class EntityMetaCollection {

    protected $metaReader;
    protected $metaCollection = array();
    
    public function __construct(\System\Data\Entity\MetaReaders\MetaReader $metaReader){
        $this->metaReader = $metaReader;
    }
    
    public function read($entityName){
        if(array_key_exists($entityName, $this->metaCollection)){
            return $this->metaCollection[$entityName];
        }else{
            $metaData = $this->metaReader->read($entityName);
            $this->metaCollection[$entityName] = $metaData;
        }
        return $metaData;
    }
}