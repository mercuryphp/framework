<?php

namespace System\Web\Mvc;

use System\Collections\Dictionary;
use System\Web\Routing\RequestContext;
use System\Web\Mvc\ViewContext;

abstract class Controller{
    
    protected $viewBag;
    private $registry;
    private $httpContext;
    private $routeData;
    private $viewContext;
    private $view;
    private $identity;
    
    public function __construct(){
        $this->viewBag = new ViewBag();
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
    
    public function setUser(\System\Web\Security\UserIdentity $identity){
        $this->identity = $identity;
    }
    
    public function getUser(){
        return $this->identity;
    }
    
    public function execute(RequestContext $requestContext){
       
        $this->httpContext = $requestContext->getHttpContext();
        $this->routeData = $requestContext->getRouteData();
        $this->viewContext = new ViewContext($this->httpContext, $this->routeData, $this->viewBag);
        
        $routeData = $requestContext->getRouteData();
        
        $refClass = new \ReflectionClass(get_class($this));
        
        $actionName = $routeData->get('action');
        
        if($this->httpContext->getRequest()->isPost() && $refClass->hasMethod($actionName.'Post')){
            $actionName .= 'Post';
        }elseif($this->httpContext->getRequest()->isPut() && $refClass->hasMethod($actionName.'Put')){
            $actionName .= 'Put';
        }elseif($this->httpContext->getRequest()->isDelete() && $refClass->hasMethod($actionName.'Delete')){
            $actionName .= 'Delete';
        }elseif($this->httpContext->getRequest()->isAjax() && $refClass->hasMethod($actionName.'Ajax')){
            $actionName .= 'Ajax';
        }

        if(!$refClass->hasMethod($actionName)){
            throw new ActionNotFoundException(sprintf("The action '%s' does not exist", $actionName));
        }

        $actionMethod = $refClass->getMethod($actionName);
        $methodParams = $actionMethod->getParameters();
        $requestParams = $requestContext->getHttpContext()->getRequest()->getParam();

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

        $this->load();
        $output = $actionMethod->invokeArgs($this, $args);
        $this->httpContext->getResponse()->write($output);
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
        $this->httpContext->getResponse()->redirect($location);
    }
    
    public function view($viewName = null){
        if($viewName){
            $this->viewContext->getRouteData()->set('action', $viewName);
        }
        return $this->view->render($this->viewContext);
    }
    
    public function json($data, $options = null){
        $data = json_encode($data, $options);
        $this->httpContext
            ->getResponse()
            ->addHeader('Content-type' , 'application/json; charset=utf-8', false)
            ->addHeader('Content-length' , strlen($data) , false);
        return $data;
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
    
    public function load(){}
}

?>
