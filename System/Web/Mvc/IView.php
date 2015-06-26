<?php

namespace System\Web\Mvc;

interface IView{
    
    public function setViewFile($viewFile);
    public function render(ViewContext $viewContext);
}

?>
