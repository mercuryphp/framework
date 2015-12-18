<?php

namespace System\Web\Mvc;

class CollectionModelBinderAttribute extends ModelBinder {
    
    protected $model;
    
    public function __construct($paramName, $model){
        $this->paramName = $paramName;
        $this->model = $model;
    }

    public function bind(\System\Web\Mvc\ModelBindingContext $modelBindingContext){
        $collection = \System\Std\Object::toObject($modelBindingContext->getObjectName(), array());
        $post = $modelBindingContext->getRequest()->getPost();
        $fields = $post->getKeys();

        $tmp = array();
        foreach($post->toArray() as $key => $array){
            if(is_array($array)){
                foreach($array as $idx=> $value){
                    $tmp[$idx][$key] = $value;
                }
            }
        }
        
        foreach($tmp as $item){
            $collection->add(\System\Std\Object::toObject($this->model, $item));
        }
        return $collection;
    }
}