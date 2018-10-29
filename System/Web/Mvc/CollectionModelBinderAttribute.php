<?php

namespace System\Web\Mvc;

class CollectionModelBinderAttribute extends ModelBinder {
    
    protected $model;
    
    public function __construct($paramName, $model){
        $this->paramName = $paramName;
        $this->model = $model;
    }

    public function bind(\System\Web\Mvc\ModelBindingContext $modelBindingContext){
        $collection = \System\Std\Obj::toObject($modelBindingContext->getObjectName(), array());
        $post = $modelBindingContext->getRequest()->getPost()->toArray();

        $tmp = array();
        foreach($post as $key => $array){
            if(is_array($array)){
                if(strpos($key, '->') > -1){
                    list($object, $field) = explode('->', $key, 2);
                    if($object == $this->paramName){
                        $key = $field;
                    }
                }
                foreach($array as $idx=> $value){
                    $tmp[$idx][$key] = $value;
                }
            }
        }
        
        foreach($tmp as $item){
            $collection->add(\System\Std\Obj::toObject($this->model, $item));
        }
        return $collection;
    }
}