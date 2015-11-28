<?php

namespace System\Web;

use System\Std\Environment;
use System\Std\Str;
use System\Std\Object;
use System\Diagnostics\Logger;
use System\Globalization\CultureInfo;
use System\Configuration\YmlConfiguration;
use System\Web\Routing\RouteCollection;

abstract class HttpApplication {
    
    /**
     * Application root path.
     */
    private $rootPath;

    /**
     * Application configuration.
     */
    private $config = null;
    
    /**
     * Application logger.
     */
    private $logger = null;
    
    /**
     * Encapsulates Http request/response/session details.
     */
    private $httpContext;
    
    /**
     * Collection of routes.
     */
    private $routes;
    
    /**
     * Collection of routes.
     */
    private $authenticationHandler;

    /**
     * Initializes the application with a root path.
     * 
     * @param   string $rootPath
     */
    public function __construct($rootPath){
        $this->rootPath = $rootPath;
        $this->config = new YmlConfiguration('config.php'); 
        $this->routes = new RouteCollection();
        $this->logger = new Logger(new \System\Diagnostics\Handlers\OutputHandler());
        $this->authenticationHandler = new \System\Web\Security\SessionAuthenticationHandler();
    }
    
    public function setConfig(\System\Configuration\Configuration $config){
        $this->config = $config;
    }
    
    public function getConfig(){
        return $this->config;
    }   

    public function getRoutes(){
        return $this->routes;
    }
    
    public function getLogger(){
        return $this->logger;
    }
    
    public function getHttpContext(){
        return $this->httpContext;
    }
    
    public function setAuthenticationHandler(\System\Web\Security\AuthenticationHandler $authenticationHandler){
        return $this->authenticationHandler = $authenticationHandler;
    }
    
    public function getAuthenticationHandler(){
        return $this->authenticationHandler;
    }

    /**
     * The start() method is executed after the application has been instantiated
     * but before any system settings have been configured. This method is intended
     * to be overridden. 
     * 
     * @return  void
     */
    public function start(){}
    
    /**
     * Initializes the application with system settings. This method is declared 
     * final and cannot be overriden.
     * 
     * @return  void
     */
    public final function init(){
        Environment::setRootPath($this->rootPath);
        Environment::setExecutionTime($this->config->get('environment.executionTime', 30));
        Environment::setCulture(new CultureInfo($this->config->get('environment.locale', 'en')));
        Environment::setDateTimeFormat($this->config->get('environment.dateTimeFormat', 'yyyy-MM-dd HH:mm:ss'));
        Environment::setTimezone($this->config->get('environment.timezone'));

        $request = new HttpRequest();
        $response = new HttpResponse();

        $session = Object::getInstance($this->config->get('session.handler', 'System.Web.Session.FileSystem'),array($request,$response));
        $session->setName($this->config->get('session.name', 'PHPSESSID'));
        $session->setExpires($this->config->get('session.expires', 0));
        $session->setPath($this->config->get('session.path', '/'));
        $session->setDomain($this->config->get('session.domain', ''));
        $session->isSecure($this->config->get('session.secure', false));
        $session->isHttpOnly($this->config->get('session.httpOnly', true));

        $this->httpContext = new HttpContext($request, $response, $session);

        $classAlias = $this->config->get('classAlias', array());
        foreach($classAlias as $alias=>$class){
            class_alias(str_replace('.', '\\', $class), $alias, true);
        }
        
        $this->httpContext->getSession()->open();
    }
    
    /**
     * The load() method is executed after the init() method. This method must be
     * overridden to provide user defined functionality such as adding routes to 
     * the route collection.
     * 
     * @return  void
     */
    public function load(){}
    
    /**
     * The load() method is executed after the init() method. This method must be
     * overridden to provide user defined functionality such as adding routes to 
     * the route collection.
     * 
     * @return  void
     */
    public function authenticateRequest(){
        $this->authenticationHandler->authenticate();
    }

    /**
     * The preAction() method is executed before the controller action.
     * Override to provide functionality that must be invoked before the action method.
     * 
     * @param   System.Web.Mvc.Controller $controller
     * @return  void
     */
    public function preAction(\System\Web\Mvc\Controller $controller){}
    
    /**
     * The postAction() method is executed after the controller action.
     * Override to provide functionality that must be invoked after the action method.
     * 
     * @param   System.Web.Mvc.Controller $controller
     * @return  void
     */
    public function postAction(\System\Web\Mvc\Controller $controller){}

    /**
     * Dispatches a controller. This method is declared final and cannot be overriden.
     * 
     * @return  void
     */
    public final function run(){

        if($this->routes->count() == 0){
            throw new \RuntimeException('One or more routes must be registered.');
        }

        $controllerDispacthed = false;
        foreach($this->routes as $route){
            $route->setHttpRequest($this->httpContext->getRequest());
            $routeData = $route->execute();

            if($routeData){

                $class = Str::set(sprintf('%s.Controllers.%sController', $route->getNamespace(), ucfirst(strtolower($routeData->get('controller')))));
                $this->httpContext->getRequest()->getRouteData()->set('namespace', $route->getNamespace());

                try{
                    $controller = Object::getInstance((string)$class);
                }catch(\ReflectionException $e){
                    throw new Mvc\ControllerNotFoundException($this->httpContext);
                }
                
                if(!$controller instanceof Mvc\Controller){
                    throw new Mvc\MvcException(sprintf("The controller '%s' does not inherit from System\Web\Mvc\Controller.", $class));
                }
                
                $controller->setConfig($this->config);
                $controller->setLogger($this->logger);
                $controller->setHttpContext($this->httpContext);
                $controller->setAuthenticationHandler($this->authenticationHandler);
                $controller->getRegistry()->merge(Object::getProperties($this, \ReflectionProperty::IS_PUBLIC |  \ReflectionProperty::IS_PROTECTED));
                $this->authenticationHandler->setHttpContext($this->httpContext);
                
                $this->authenticateRequest();
                $this->preAction($controller);
                
                $moduleClassName = Str::set(sprintf('%s.Controllers.%s', $route->getNamespace(), 'Module'));
                $moduleInstance = Object::getInstance($moduleClassName, array(), false);

                if($moduleInstance){
                    if (method_exists($moduleInstance, 'load')){
                        $moduleInstance->load($controller);
                    }
                }

                $controller->execute();

                if($moduleInstance){
                    if (method_exists($moduleInstance, 'unload')){
                        $moduleInstance->unload($controller);
                    }
                }
                
                $this->postAction($controller);
                $controllerDispacthed = true;
                break;
            }
        }
        
        if(false === $controllerDispacthed){
            throw new Mvc\MvcException("Unable to dispatch a controller. None of the registered routes matched the request URI.");
        }
    }
    
    /**
     * Executed when an exception is thrown. Override to provide custom error handling.
     * 
     * @param   Exception $e
     * @return  void
     */
    public function error(\Exception $e){}
    
    /**
     * The end() method is executed at the end of the application cycle. 
     * Any output is sent to the browser.
     * 
     * @return  void
     */
    public function end(){
        $this->httpContext->getSession()->write();
        $this->httpContext->getResponse()->endFlush();
    }
}