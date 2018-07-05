<?php

namespace System\Web\Mvc;

use System\Std\Environment;
use System\Std\Str;

class NativeView implements IView {

    protected $viewFile;
    protected $layoutFile;
    protected $scripts = array();
    protected $output = array();
    protected $viewFilePattern = '/{n}/{m}Views/{c}/{a}';
    protected $escaper;
    protected $dynamicMethods = array();
    
    public function __construct(){
        $this->setEscaper(function($value){
            return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
        });
    }
    
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
    
    public function setViewFile($viewFile){
        $this->viewFile = $viewFile;
        return $this;
    }
    
    public function setEscaper(callable $escaper){
        $this->escaper = $escaper;
        \System\Web\UI\Html::setEscaper($escaper);
    }
    
    public function getEscaper(){
        return $this->escaper;
    }
    
    protected function escape($value){
        return call_user_func_array($this->escaper, array($value));
    }
    
    public function addMethod($name, $cloure){
        $this->dynamicMethods[$name] = $cloure;
    }
    
    public function getMethods(){
        return $this->dynamicMethods;
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
        $request = $viewContext->getHttpContext()->getRequest();
        $response = $viewContext->getHttpContext()->getResponse();
        $routeData = $viewContext->getRouteData();

        $viewFile = Str::set($this->viewFilePattern)->template(
            array(
                'n' => $routeData->getString('namespace')->replace('.', '/'),
                'm' => $routeData->getString('module')->toLower()->toUpperFirst()->append('/', true),
                'c' => $routeData->getString('controller')->toLower()->toUpperFirst(),
                'a' => $routeData->getString('action')->toLower()->toUpperFirst()
            )
        )->prepend(Environment::getRootPath())->append('.php');

        $viewFile = $this->viewFile ? $this->viewFile : $viewFile;
        
        if(is_file($viewFile)){
            extract($viewContext->getViewBag()->toArray());
            ob_start();
            
            require_once $viewFile;
            $this->output['view'] = ob_get_clean();

            if($this->layoutFile){
                if(substr($this->layoutFile, 0, 1) == '~'){
                    $this->layoutFile = \System\Std\Environment::getRootPath() . substr($this->layoutFile, 1);
                }
                
                if (is_file($this->layoutFile)){
                    ob_start();
                    require_once $this->layoutFile;
                    $this->output['layoutFile'] = ob_get_clean();
                }else{
                    throw new ViewNotFoundException("The Layout file '%s' was not found", $this->layoutFile);
                }
            }

            if(isset($this->output['layoutFile'])){
                return $this->output['layoutFile'];
            }
            
            return $this->output['view'];
        }else{
            throw new ViewNotFoundException("The View '%s' was not found.", (string)$viewFile);
        }
    }
    
    public function __call($name, $arguments){
        if(array_key_exists($name, $this->dynamicMethods)){
            return call_user_func_array($this->dynamicMethods[$name], $arguments);
        }else{
            throw new \Exception('Call to undefined method ' . get_class($this) .'::'.$name.'()');
        }
    }
}