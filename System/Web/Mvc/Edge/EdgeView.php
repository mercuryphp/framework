<?php

namespace System\Web\Mvc\Edge;

use System\Std\Environment;
use System\Std\String;

class EdgeView extends \System\Web\Mvc\NativeView {

    public function render(\System\Web\Mvc\ViewContext $viewContext){
        $request = $viewContext->getHttpContext()->getRequest();
        $response = $viewContext->getHttpContext()->getResponse();
        $routeData = $viewContext->getRouteData();

        $viewFile = String::set($this->viewFilePattern)
            ->prepend(Environment::getControllerPath())
            ->replace('@module', String::set($routeData->module)->toLower()->toUpperFirst())
            ->replace('@controller', String::set($routeData->controller)->toLower()->toUpperFirst())
            ->replace('@action', String::set($routeData->action)->toLower()->toUpperFirst())
            ->append('.php')
            ->replace('\\', '/');
        
        if(is_file($viewFile)){
            $compiledFileName = "C:\\xampp\\htdocs\\cloud\\Views\\Resources\\" . sha1((string)$viewFile);
            
            if(!is_file($compiledFileName) || true){
                $lexer = new Lexer($viewFile);
                $compiler = new Compiler($lexer);
                file_put_contents($compiledFileName, $compiler->compile());
                $viewFile = $compiledFileName;
            }else{
                $viewFile = $compiledFileName;
            }
            
            extract($viewContext->getViewBag()->toArray());

            ob_start();
            
            require_once $viewFile;
            $this->output["view"] = ob_get_clean();

            if($this->layoutFile){
                if(substr($this->layoutFile, 0, 1) == '~'){
                    $this->layoutFile = \System\Std\Environment::getControllerPath() . substr($this->layoutFile, 1);
                }
                
                if (is_file($this->layoutFile)){
                    $compiledFileName = "C:\\xampp\\htdocs\\cloud\\Views\\Resources\\" . sha1((string)$this->layoutFile);
                    
                    if(!is_file($compiledFileName) || true){
                        $lexer = new Lexer($this->layoutFile);
                        $compiler = new Compiler($lexer);
                        file_put_contents($compiledFileName, $compiler->compile());
                        $this->layoutFile = $compiledFileName;
                    }else{
                        $this->layoutFile = $compiledFileName;
                    }
                    
                    ob_start();
                    require_once $this->layoutFile;
                    $this->output['layoutFile'] = ob_get_clean();
                }else{
                    throw new \System\Web\Mvc\ViewNotFoundException(sprintf("The layout file '%s' was not found",$this->layoutFile));
                }
            }

            if(isset($this->output['layoutFile'])){
                return $this->output['layoutFile'];
            }
            
            return $this->output['view'];
        }else{
            throw new \System\Web\Mvc\ViewNotFoundException(sprintf("The view '%s' was not found.", $viewFile));
        }
    }
}