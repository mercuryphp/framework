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
    protected $config = null;
    
    /**
     * Application logger.
     */
    protected $logger = null;
    
    /**
     * Encapsulates Http request/response/session details.
     */
    protected $httpContext;
    
    /**
     * Collection of routes.
     */
    protected $routes;

    /**
     * Initializes the application with a root path.
     * 
     * @param   string $rootPath
     */
    public function __construct($rootPath){
        $this->rootPath = $rootPath;
        $this->config = new YmlConfiguration('config.php'); 
        $this->logger = new Logger(new \System\Diagnostics\Handlers\OutputHandler());
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

        $this->routes = new RouteCollection();

        Environment::setRootPath($this->rootPath);
        Environment::setControllerPath($this->rootPath);
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
            class_alias(str_replace('.', '\\', $class), $alias, false);
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

                $namespace = '';
                if(Environment::getRootPath() != Environment::getControllerPath()){
                    $namespace = Str::set(Environment::getControllerPath())->replace(Environment::getRootPath(), '')->trim('/');
                }

                $moduleName = $routeData->get('module') ? $routeData->get('module').'.' : '';
                $class = Str::set(sprintf('%s.%sControllers.%sController', $namespace, ucfirst(strtolower($moduleName)), ucfirst(strtolower($routeData->get('controller')))));

                try{
                    $controller = Object::getInstance((string)$class);
                    $controller->setHttpContext($this->httpContext);
                    $controller->getRegistry()->merge(get_object_vars($this));
                }catch(\ReflectionException $e){
                    throw new Mvc\ControllerNotFoundException($this->httpContext);
                }
                
                if(!$controller instanceof Mvc\Controller){
                    throw new Mvc\MvcException(sprintf("The controller '%s' does not inherit from System\Web\Mvc\Controller.", $class));
                }

                $moduleClassName = Str::set(sprintf('%s.%sControllers.%s', $namespace, ucfirst($moduleName), 'Module'));

                $this->preAction($controller);

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