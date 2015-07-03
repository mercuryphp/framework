<?php

namespace System\Web\Mvc;

use System\Std\Environment;
use System\Std\String;

class NativeView implements IView{

    protected $layoutFile;
    protected $scripts = array();
    protected $output = array();
    protected $viewFilePattern = '@module/Views/@controller/@action';
    
    public function addScript($type, $script){
        $this->scripts[$type][] = $script;
    }
    
    public function setViewFilePattern($viewFilePattern){
        $this->viewFilePattern = $viewFilePattern;
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
        $routeData = $viewContext->getRouteData();

        $viewFile = String::set($this->viewFilePattern)
            ->prepend(Environment::getAppPath())
            ->replace('@module', String::set($routeData->module)->toLower()->toUpperFirst())
            ->replace('@controller', String::set($routeData->controller)->toLower()->toUpperFirst())
            ->replace('@action', String::set($routeData->action)->toLower()->toUpperFirst())
            ->append('.php')
            ->replace('//', '/');
                
        if(file_exists($viewFile)){
            extract($viewContext->getViewBag()->toArray());

            ob_start();
            
            require_once $viewFile;
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
            throw new ViewNotFoundException(sprintf("The view '%s' was not found.", $viewFile));
        }
    }
}

?>
