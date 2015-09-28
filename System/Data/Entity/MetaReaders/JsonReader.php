<?php

namespace System\Data\Entity\MetaReaders;

use System\Std\Str;
use System\Std\Object;

class JsonReader extends MetaReader {
    
    protected $path;
    
    /**
     * Initializes an instance of JsonReader with a path to the entity meta file.
     * 
     * @param   string $path
     */
    public function __construct($path){
        $this->path = $path;
    }
    
    /**
     * Gets a EntityMeta object for the specified $entityName.  
     * 
     * @param   string $entityName
     * @return  System.Data.Entity.EntityMeta
     */
    public function read($entityName){
        $path = Str::set($this->path)
                ->replace('\\', '/')
                ->rightTrim('/')
                ->append('/'.$entityName)
                ->replace('.', '/')
                ->append('.json');

        if(is_file((string)$path)){
            $meta = array();
            $data = json_decode(file_get_contents((string)$path), true);

            $tableName = isset($data['Table']) ? $data['Table'] : new Str(Str::set($entityName)->toLower()->split('\.')->last());
            $key = isset($data['Key']) ? $data['Key'] : $tableName.'_id';
            
            $meta['System.Data.Entity.Attributes.Table'] = new \System\Data\Entity\Attributes\Table((string)$tableName);
            $meta['System.Data.Entity.Attributes.Key'] = new \System\Data\Entity\Attributes\Key($key);
            $meta['Columns'] = array();
            
            $columns = isset($data['Columns']) ? $data['Columns'] : array();
            foreach($columns as $columnName => $attributes){
                foreach($attributes as $strAttr){
                    $attribute = Str::set($strAttr)->get('@', '(');
                    
                    if((string)$attribute){
                        $args = Str::set($strAttr)->get('(', ')');

                        if(!$attribute->indexOf('.')){
                            $attribute = $attribute->prepend('System.Data.Entity.Attributes.');
                        }

                        try{ 
                            $meta['Columns'][$columnName][(string)$attribute] = Object::getInstance((string)$attribute, $this->getArgs($args));
                        }catch(\Exception $e){
                            throw new \System\Data\Entity\EntityException('The attribute "'.(string)$attribute.'" does not exist.');
                        }
                    }
                }
            }

            $metaData = new \System\Data\Entity\EntityMeta((string)$entityName, $meta);
            return $metaData;
        }
    }
}