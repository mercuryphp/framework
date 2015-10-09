<?php

namespace System\Web\Mvc;

abstract class PostActionAttribute {
    public abstract function execute(\System\Web\Mvc\Controller $controller);
}