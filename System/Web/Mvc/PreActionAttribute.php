<?php

namespace System\Web\Mvc;

abstract class PreActionAttribute {
    public abstract function execute(\System\Web\Mvc\Controller $controller, \System\Collections\Dictionary $actionArgs);
}