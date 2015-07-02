<?php

namespace System\Web\Mvc;

interface IView {
    public function render(ViewContext $viewContext);
}

?>
