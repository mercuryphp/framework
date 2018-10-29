<?php

namespace System\Web\Mvc;

class DefaultModelBinderAttribute extends ModelBinder {

    public function bind(\System\Web\Mvc\ModelBindingContext $modelBindingContext){
        return \System\Std\Obj::toObject($modelBindingContext->getObjectName(), $modelBindingContext->getRequest()->toArray());
    }
}