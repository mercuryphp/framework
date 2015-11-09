<?php

namespace System\Web\Mvc;

abstract class PreActionAttribute {
    public abstract function execute(\System\Web\Mvc\Controller $controller, array $actionArgs = array());
}