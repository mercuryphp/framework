<?php

namespace System\Web\Mvc;

class NativeView implements IView{
    
    protected $viewFile;
    protected $layoutFile;
    protected $scripts = array();
    protected $output = array();
    
    public function addScript($type, $script){
        $this->scripts[$type][] = $script;
    }
    
    public function setViewFile($viewFile){
        $this->viewFile = $viewFile;
        return $this;
    }
    
    public function setLayout($layoutFile){
        $this->layoutFile = $layoutFile;
        return $this;
    }
    
    public function renderScripts(){
        foreach($this->scripts as $type=>$scripts){
            foreach($scripts as $script){
                switch($type){
                    case 'css':
                        echo '<link rel="stylesheet" href="'.$script.'" />'.PHP_EOL;
                        break;
                    case 'js':
                        echo '<script src="'.$script.'" type="text/javascript"></script>'.PHP_EOL;
                        break;
                }
            }
        }
    }
    
    public function renderBody(){
        if(isset($this->output['view'])){
            echo $this->output['view'];
        }else{
            throw new \RuntimeException("The renderBody() method can only be called from a layout file.");
        }
    }
    
    public function render(ViewContext $viewContext){
        if(file_exists($this->viewFile)){
            $request = $viewContext->getHttpContext()->getRequest();
            extract($viewContext->getViewBag()->toArray());

            ob_start();
            
            require_once $this->viewFile;
            $this->output['view'] = ob_get_clean();

            if($this->layoutFile){
                
                if(substr($this->layoutFile, 0, 1) == '~'){
                    $this->layoutFile = \System\Std\Environment::getAppPath() . substr($this->layoutFile, 1);
                }
                
                if (file_exists($this->layoutFile)){
                    ob_start();
                    require_once $this->layoutFile;
                    $this->output['layoutFile'] = ob_get_clean();
                }else{
                    throw new ViewNotFoundException(sprintf("The layout file '%s' was not found",$this->layoutFile));
                }
            }

            if(isset($this->output['layoutFile'])){
                return $this->output['layoutFile'];
            }
            
            return $this->output['view'];
        }else{
            throw new ViewNotFoundException(sprintf("The view '%s' was not found.", $this->viewFile));
        }
    }
}

?>
