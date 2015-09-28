<?php

namespace System\Data\Entity;

class EntityMetaCollection {

    protected $metaReader;
    protected $metaCollection = array();
    
    /**
     * Initializes an instance of EntityMetaCollection with an instance of
     * System.Data.Entity.MetaReaders.MetaReader. This class stores a collection 
     * of entity meta data. 
     * 
     * @param   System.Data.Entity.MetaReaders.MetaReader $metaReader
     */
    public function __construct(\System\Data\Entity\MetaReaders\MetaReader $metaReader){
        $this->metaReader = $metaReader;
    }
    
    /**
     * Sets an instance of System.Data.Entity.MetaReaders.MetaReader.
     * 
     * @param   System.Data.Entity.MetaReaders.MetaReader $metaReader
     * @return  void
     */
    public function setMetaReader(\System\Data\Entity\MetaReaders\MetaReader $metaReader){
        $this->metaReader = $metaReader;
    }
    
    /**
     * Gets an EntityMeta object for the specified $entityName. If the 
     * EntityMeta object does not exist in the collection, then it is read using 
     * the MetaReader object. Subsequent calls for the same meta data will be 
     * retrieved from the collection.
     * 
     * @param   string $entityName
     * @return  System.Data.Entity.EntityMeta
     */
    public function get($entityName){
        if(array_key_exists($entityName, $this->metaCollection)){
            return $this->metaCollection[$entityName];
        }else{
            $metaData = $this->metaReader->read($entityName);
            $this->metaCollection[$entityName] = $metaData;
        }
        return $metaData;
    }
    
    /**
     * Gets an array of EntityMeta objects stored in the collection.
     * 
     * @return  array
     */
    public function toArray(){
        return $this->metaCollection;
    }
}