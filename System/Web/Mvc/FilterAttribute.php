<?php

namespace System\Web\Mvc;

abstract class FilterAttribute {
    public abstract function isValid(\System\Web\HttpContext $httpContext);
}

