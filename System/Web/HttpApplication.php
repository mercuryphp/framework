<?php

namespace System\Web;

use System\Std\Environment;
use System\Std\Str;
use System\Std\Object;
use System\Log\Logger;
use System\Globalization\CultureInfo;
use System\Configuration\YmlConfiguration;
use System\Web\Routing\RouteCollection;
use System\Web\Security\Authentication;
use System\Web\Security\UserIdentity;

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
     * @method  __construct
     * @param   string $rootPath
     */
    public function __construct($rootPath){
        $this->rootPath = $rootPath;
        $this->config = new YmlConfiguration('config.php'); 
        $this->logger = new Logger(new \System\Log\Handlers\OutputHandler());
    }

    /**
     * The start() method is executed after the application has been instantiated
     * but before any system settings have been configured. This method is intended
     * to be overridden. 
     * 
     * @method  start
     * @return  void
     */
    public function start(){}
    
    /**
     * Initializes the application with system settings. This method is declared 
     * final and cannot be overriden.
     * 
     * @method  init
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
        Environment::setNamespaces($this->config->get('namespaces', array()));
        Environment::setDefaultConnectionString($this->config->get('connectionStrings.default'));

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
        
        Authentication::setCookieName($this->config->get('formsAuthentication.cookieName', 'PHPXAUTH'));
        Authentication::setHashAlgorithm($this->config->get('formsAuthentication.hashAlgorithm'));
        Authentication::setCipher($this->config->get('formsAuthentication.cipher'));
        Authentication::setEncryptionKey($this->config->get('formsAuthentication.encryptionKey'));
        Authentication::setValidationKey($this->config->get('formsAuthentication.validationKey')); 
        
        $this->httpContext->getSession()->open();
    }
    
    /**
     * The load() method is executed after the init() method. This method must be
     * overridden to provide user defined functionality such as adding routes to 
     * the route collection.
     * 
     * @method  load
     * @return  void
     */
    public function load(){}

    /**
     * Authenticates a HTTP request using Authentication and establishes the 
     * identity of the user.
     * 
     * @method  authenticateRequest
     * @param   System.Web.Mvc.Controller $controller
     * @return  void
     */
    public function authenticateRequest(\System\Web\Mvc\Controller $controller){
        $httpAuthCookie = $this->httpContext->getRequest()->getCookies()->get(Authentication::getCookieName());
        
        $identity = new UserIdentity('Anonymous');
        
        if($httpAuthCookie){
            $ticket = Authentication::decrypt($httpAuthCookie->getValue()); 

            if($ticket && ((\System\Std\Date::now()->getTimestamp() < $ticket->getExpire()) || $ticket->getExpire()==0)){
                $identity = new UserIdentity($ticket->getName(), $ticket->getUserData(), true);
            }
        }
        $this->httpContext->getRequest()->setUser($identity);
    }
    
    /**
     * The preAction() method is executed before the controller action.
     * Override to provide functionality that must be invoked before the action method.
     * 
     * @method  preAction
     * @param   System.Web.Mvc.Controller $controller
     * @return  void
     */
    public function preAction(\System\Web\Mvc\Controller $controller){}
    
    /**
     * The postAction() method is executed after the controller action.
     * Override to provide functionality that must be invoked after the action method.
     * 
     * @method  postAction
     * @param   System.Web.Mvc.Controller $controller
     * @return  void
     */
    public function postAction(\System\Web\Mvc\Controller $controller){}

    /**
     * Dispatches a controller. This method is declared final and cannot be overriden.
     * 
     * @method  run
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
                    $controller = Object::getInstance($class);
                    $controller->setHttpContext($this->httpContext);
                    $controller->getRegistry()->merge(get_object_vars($this));
                }catch(\ReflectionException $e){
                    throw new Mvc\ControllerNotFoundException(sprintf("The controller '%s' does not exist.", $class));
                }
                
                if(!$controller instanceof Mvc\Controller){
                    throw new Mvc\MvcException(sprintf("The controller '%s' does not inherit from System\Web\Mvc\Controller.", $class));
                }

                $moduleClassName = Str::set(sprintf('%s.%sControllers.%s', $namespace, ucfirst($moduleName), 'Module'));

                $this->authenticateRequest($controller);

                $this->preAction($controller);

                $moduleInstance = Object::getInstance($moduleClassName, null, false);

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
     * @method  error
     * @param   Exception $e
     * @return  void
     */
    public function error(\Exception $e){}
    
    /**
     * The end() method is executed at the end of the application cycle. 
     * Any output is sent to the browser.
     * 
     * @method  end
     * @return  void
     */
    public function end(){
        $this->httpContext->getSession()->write();
        $this->httpContext->getResponse()->flush();
        exit;
    }
}