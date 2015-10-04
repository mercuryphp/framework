<?php

namespace System\Web\Mvc;

abstract class ReturnAttribute {
    
    public abstract function isValid(\System\Web\HttpContext $httpContext);
}

