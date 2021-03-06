<?php

namespace System\Web\Mvc;

use System\Std\Str;
use System\Collections\Dictionary;
use System\Web\Mvc\ViewContext;

abstract class Controller{
    
    private $attributes = array();
    private $registry;
    private $config;
    private $logger;
    private $httpContext;
    private $authenticationHandler;
    private $view;
    protected $viewBag;
    
    /**
     * Initializes a new instance of the Controller class.
     * 
     * @method  __construct
     */
    public function __construct(){
        $this->registry = new Dictionary();
        $this->view = new NativeView();
        $this->viewBag = new Dictionary();
    }
    
    /**
     * Get a collection of dynamic controller properties.
     * 
     * @return  System.Collection.Dictionary
     */
    public function getRegistry(){
        return $this->registry;
    }
    
    /**
     * Sets the view engine.
     * 
     * @param   System.Web.Mvc.IView
     * @return  void
     */
    public function setViewEngine(IView $view){
        $this->view = $view;
    }
    
    /**
     * Gets the view engine.
     * 
     * @return  System.Web.Mvc.IView
     */
    public function getViewEngine(){
        return $this->view;
    }
    
    /**
     * Get a collection of view data.
     * 
     * @return  System.Collection.Dictionary
     */
    public function getViewBag(){
        return $this->viewBag;
    }
    
    /**
     * Sets the configuration object.
     * 
     * @param   System.Configuration.Configuration
     * @return  void
     */
    public function setConfig(\System\Configuration\Configuration $config){
        $this->config = $config;
    }
    
    /**
     * Gets the configuration object.
     * 
     * @return  System.Configuration.Configuration
     */
    public function getConfig(){
        return $this->config;
    }
    
    /**
     * Sets the logger object.
     * 
     * @param   System.Diagnostics.Logger
     * @return  void
     */
    public function setLogger(\System\Diagnostics\Logger $logger){
        $this->logger = $logger;
    }
    
    /**
     * Gets the logger object.
     * 
     * @return  System.Diagnostics.Logger
     */
    public function getLogger(){
        return $this->logger;
    }

    /**
     * Sets the HttpContext.
     * 
     * @param   System.Web.HttpContext
     */
    public function setHttpContext(\System\Web\HttpContext $httpContext){
        $this->httpContext = $httpContext;
    }

    /**
     * Gets the HttpContext.
     * 
     * @return  System.Web.HttpContext
     */
    public function getHttpContext(){
        return $this->httpContext;
    }
    
    /**
     * Gets the HttpRequest.
     * 
     * @return  System.Web.HttpRequest
     */
    public function getRequest(){
        return $this->httpContext->getRequest();
    }
    
    /**
     * Gets the HttpResponse.
     * 
     * @return  System.Web.HttpResponse
     */
    public function getResponse(){
        return $this->httpContext->getResponse();
    }
    
    /**
     * Gets the session handler.
     * 
     * @return  System.Web.Session.Session
     */
    public function getSession(){
        return $this->httpContext->getSession();
    }
    
    public function setAuthenticationHandler(\System\Web\Security\AuthenticationHandler $authenticationHandler){
        return $this->authenticationHandler = $authenticationHandler;
    }
    
    public function getAuthenticationHandler(){
        return $this->authenticationHandler;
    }
    
    /**
     * Get the user for the current request.
     * 
     * @return  System.Web.Security.UserIdentity
     */
    public function getUser(){
        return $this->getRequest()->getUser();
    }

    /**
     * Creates and gets a RedirectResult object that redirects to the 
     * specified $location.
     * 
     * @return  System.Web.Mvc.RedirectResult
     */
    public function redirect($location){
        return new RedirectResult($this->httpContext, $location);
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
    
    public function addAttribute($attribute){
        $this->attributes[] = $attribute; 
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

        $this->load();
        $attributes = array_merge($this->attributes, \System\Std\Obj::getMethodAnnotations($this, $actionName));
        $modelBinders = new \System\Collections\Dictionary();

        $actionMethod = $refClass->getMethod($actionName);
        $methodParams = $actionMethod->getParameters();
        $methodArgs = new \System\Collections\Dictionary();
        
        foreach($attributes as $idx=>$attribute){
            if($attribute instanceof FilterAttribute && !$attribute->isValid($this->httpContext)){
                return false;
            }
            elseif($attribute instanceof ModelBinder){
                $modelBinders->add($attribute->getParameterName(), $attribute);
                unset($attributes[$idx]);
            }
        }

        foreach($methodParams as $param){
            $object = $param->getClass();
            if(is_object($object)){
                try {
                    $modelBinder = $modelBinders->get($param->getName(), new DefaultModelBinderAttribute());
                    $methodArgs[$param->getName()] = $modelBinder->bind(new ModelBindingContext($this->httpContext->getRequest(), $object->getName(), $param->isOptional(), null));
                }catch(\Exception $e){
                    throw new ModelBinderException(sprintf("Model binding on parameter '%s' failed. %s", $param->getName(), $e->getMessage()));
                }
            }else{
                $value = $this->httpContext->getRequest()->getParam($param->getName());
                if(strlen($value) == 0 && $param->isOptional()){
                    $value = $param->getDefaultValue();
                }
                $methodArgs[$param->getName()] = $value;
            }
        }

        foreach($attributes as $idx=>$attribute){
            if($attribute instanceof PreActionAttribute){
                $attribute->execute($this, $methodArgs);
                unset($attributes[$idx]);
            }
        }

        $actionResult = $actionMethod->invokeArgs($this, $methodArgs->toArray());

        if(!$actionResult){
            $actionResult = new ActionResult();
        }elseif(!$actionResult instanceof ActionResult){
            $actionResult = new StringResult($actionResult);
        }
        
        $this->render($actionResult);
        $this->unload();
        
        foreach($attributes as $idx=>$attribute){
            if($attribute instanceof PostActionAttribute){
                $attribute->execute($this);
                unset($attributes[$idx]);
            }
        }
    }
    
    public function load(){}
    
    public function unload(){}

    public function render($actionResult){
        $this->httpContext->getResponse()->write($actionResult->execute());
    }
}