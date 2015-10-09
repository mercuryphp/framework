<?php

namespace System\Web\Mvc;

use System\Std\Str;
use System\Collections\Dictionary;
use System\Web\Mvc\ViewContext;

abstract class Controller{
    
    protected $viewBag;
    private $registry;
    private $httpContext;
    private $view;
    
    /**
     * Initializes a new instance of the Controller class.
     * 
     * @method  __construct
     */
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

    public function getRegistry(){
        return $this->registry;
    }
    
    public function getUser(){
        return $this->httpContext->getRequest()->getUser();
    }
    
    public function setHttpContext(\System\Web\HttpContext $httpContext){
        $this->httpContext = $httpContext;
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
    
    public function xml($data, array $options = array()){
        return new XmlResult($this->httpContext->getResponse(), $data, $options);
    }
    
    public function view($arg = null){
        return new ViewResult($this, new ViewContext($this->httpContext, $this->viewBag, $arg));
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
    
    public function execute(array $routeData = array()){
        $controllerClass = Str::set(get_called_class())->get('\\', 'Controller', Str::LAST_LAST);
        $this->httpContext->getRequest()
            ->getRouteData()
            ->set('controller', (string)$controllerClass->toLower())
            ->merge($routeData);

        $refClass = new \ReflectionClass(get_class($this));
        $actionName = $this->httpContext->getRequest()->getRouteData()->get('action');

        if(!$refClass->hasMethod($actionName)){
            throw new ActionNotFoundException($this->httpContext);
        }

        $attributes = \System\Std\Object::getMethodAnnotations($this, $actionName);
        $modelBinders = new \System\Collections\Dictionary();
        
        foreach($attributes as $attribute){
            if($attribute instanceof FilterAttribute && !$attribute->isValid($this->httpContext)){
                return;
            }
            elseif($attribute instanceof ModelBinderAttribute){
                $modelBinders->add($attribute->getParameterName(), $attribute);
            }
        }

        $actionMethod = $refClass->getMethod($actionName);
        $methodParams = $actionMethod->getParameters();
        $methodArgs = array();
        
        foreach($methodParams as $param){
            $object = $param->getClass();

            if(is_object($object)){
                try {
                    $modelBinder = $modelBinders->get($param->getName(), new DefaultModelBinder());
                    $methodArgs[] = $modelBinder->bind(new ModelBindingContext($this->httpContext->getRequest(), $object->getName(), $param->isOptional(), null));
                }catch(\Exception $e){
                    throw new ModelBinderException(sprintf("Model binding on parameter '%s' failed. %s", $param->getName(), $e->getMessage()));
                }
            }else{
                $value = $this->httpContext->getRequest()->getParam($param->getName());

                if(strlen($value) == 0 && $param->isOptional()){
                    $value = $param->getDefaultValue();
                }
                $methodArgs[] = $value;
            }
        }

        $this->load();
        $actionResult = $actionMethod->invokeArgs($this, $methodArgs);

        if(!$actionResult){
            $actionResult = new ActionResult();
        }elseif(!$actionResult instanceof ActionResult){
            $actionResult = new StringResult($actionResult);
        }
        
        $this->render($actionResult);
        
        foreach($attributes as $attribute){
            if($attribute instanceof PostActionAttribute){
                $attribute->execute($this);
            }
        }
    }
    
    public function load(){}
    
    public function render($actionResult){
        $this->httpContext->getResponse()->write($actionResult->execute());
    }
}