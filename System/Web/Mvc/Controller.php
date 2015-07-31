<?php

namespace System\Web\Mvc;

use System\Std\Debug;
use System\Std\String;
use System\Collections\Dictionary;
use System\Web\HttpContext;
use System\Web\Mvc\ViewContext;

abstract class Controller{
    
    protected $viewBag;
    private $registry;
    private $httpContext;
    private $view;
    
    public function __construct(){
        $this->view = new NativeView();
        $this->viewBag = new Dictionary();
        $this->registry = new Dictionary();
    }
    
    public function setViewEngine(IView $view){
        $this->view = $view;
    }
    
    public function getViewEngine(){
        return $this->view;
    }
    
    public function getViewBag(){
        return $this->viewBag;
    }

    public function getUser(){
        return $this->httpContext->getRequest()->getUser();
    }
    
    public function execute(HttpContext $httpContext, array $routeData = array()){

        $this->httpContext = $httpContext;
        $routeData = $httpContext->getRequest()
            ->getRouteData()
            ->set('controller', (string)String::set(get_called_class())->get('\\', 'Controller', String::LAST_LAST)->toLower())
            ->merge($routeData);

        $refClass = new \ReflectionClass(get_class($this));
        $actionName = $routeData->get('action');
        
        $requestMethod = $this->httpContext->getRequest()->getHttpMethod();
        
        if(in_array($requestMethod, array('POST', 'PUT', 'DELETE', 'AJAX')) && $refClass->hasMethod($actionName.$requestMethod)){
            $actionName .= ucfirst(strtolower($requestMethod));
        }

        if(!$refClass->hasMethod($actionName)){
            throw new ActionNotFoundException(sprintf("The action '%s' does not exist in '%s'", $actionName, get_called_class()));
        }

        $actionMethod = $refClass->getMethod($actionName);
        $methodParams = $actionMethod->getParameters();
        $requestParams = $this->httpContext->getRequest()->getParam();

        $args = array();

        foreach($methodParams as $param){
            $object = $param->getClass();

            if(is_object($object)){
                $modelName = $object->getName();
                $params = $this->httpContext->getRequest()->toArray();

                $refClass = new \ReflectionClass($modelName);
                $modelInstance = $refClass->newInstanceArgs(array());
                $properties = $refClass->getProperties();

                foreach($properties as $property){
                    $property->setAccessible(true);
                    $name = $property->getName();

                    if (array_key_exists($name, $params)){
                        $property->setValue($modelInstance, $params[$name]);
                    }
                }
                $args[] = $modelInstance;
            }else{

                $value = $requestParams->get($param->getName());

                if(strlen($value) == 0 && $param->isOptional()){
                    $value = $param->getDefaultValue();
                }
                $args[] = $value;
            }
        }

        Debug::log(get_called_class() .':load()', Debug::EVENT);
        $this->load();
        
        Debug::log(get_called_class() .':'.$actionName.'()', Debug::EVENT);
        $actionResult = $actionMethod->invokeArgs($this, $args);

        if(!$actionResult){
            $actionResult = new ActionResult();
        }elseif(!$actionResult instanceof ActionResult){
            $actionResult = new StringResult($actionResult);
        }

        Debug::log(get_called_class() .':render()', Debug::EVENT);
        $this->render($actionResult);
    }
    
    public function getHttpContext(){
        return $this->httpContext;
    }
    
    public function getRequest(){
        return $this->httpContext->getRequest();
    }
    
    public function getResponse(){
        return $this->httpContext->getResponse();
    }
    
    public function getSession(){
        return $this->httpContext->getSession();
    }
    
    public function redirect($location){
        return new RedirectResult($this->httpContext->getResponse(), $location);
    }
    
    public function json($data, $options = null){
        return new JsonResult($this->httpContext->getResponse(), $data, $options);
    }
    
    public function view($viewName = null){
        return new ViewResult($this, new ViewContext($this->httpContext, $this->viewBag, $viewName));
    }

    public function load(){}
    
    public function render($actionResult){
        $this->httpContext->getResponse()->write($actionResult->execute());
    }
    
    public function __set($key, $value){
        $this->registry->set($key, $value);
    }
    
    public function __get($key){
        if($this->registry->hasKey($key)){
            return $this->registry->get($key);
        }
        throw new \RuntimeException(sprintf("Property '%s' does not exist in controller registry", $key));
    }
}