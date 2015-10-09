<?php

namespace System\Web\Mvc;

class DefaultModelBinder extends ModelBinder {

    public function bind(\System\Web\Mvc\ModelBindingContext $modelBindingContext){
        return \System\Std\Object::toObject($modelBindingContext->getObjectName(), $modelBindingContext->getRequest()->toArray());
    }
}