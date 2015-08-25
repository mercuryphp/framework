<?php

namespace System\Web;

use System\Std\Environment;
use System\Std\String;
use System\Std\Object;
use System\Log\Logger;
use System\Globalization\CultureInfo;
use System\Configuration\Configuration;
use System\Web\Routing\RouteCollection;
use System\Web\Security\FormsAuthentication;
use System\Web\Security\UserIdentity;

/**
 * HttpApplication
 * 
 * Executes and dispatches controller/action
 *
 * @license http://www.mercuryphp.com/license
 */
abstract class HttpApplication {
    
    /**
     * Application root path
     *
     */
    private $rootPath;

    /**
     * Application configuration.
     *
     * @var System\Configuration\Configuration
     */
    private $config = null;
    
    /**
     * Application logger.
     *
     * @var System\Log\Logger
     */
    private $logger = null;
    
    /**
     * Encapsulates Http request/response/server details.
     *
     * @var System\Web\HttpContext
     */
    private $httpContext;
    
    /**
     * Collection of routes.
     *
     * @var System\Collections\Dictionary.
     */
    private $routes;

    public function __construct($rootPath){
        $this->rootPath = $rootPath;
        $this->config = new \System\Configuration\YmlConfiguration('config.php'); 
        $this->logger = new Logger(new \System\Log\Handlers\ExceptionHandler);
    }

    /**
     * Instantiates the application configuration object.
     */
    public function start(){}
    
    /**
     * Initializes application settings.
     */
    public final function init(){

        $this->routes = new RouteCollection();

        Environment::setRootPath($this->rootPath);
        Environment::setControllerPath($this->rootPath);
        Environment::setExecutionTime($this->config->get('environment.executionTime', 30));
        Environment::setCulture(new CultureInfo($this->config->get('environment.locale', 'en')));
        Environment::setDateTimeFormat($this->config->get('environment.dateTimeFormat', 'yyyy-MM-dd HH:mm:ss'));
        Environment::setTimezone($this->config->get('environment.timezone', \System\Std\Date::now()->getTimezone()->getName()));
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
        
        FormsAuthentication::setCookieName($this->config->get('formsAuthentication.cookieName', 'PHPXAUTH'));
        FormsAuthentication::setHashAlgorithm($this->config->get('formsAuthentication.hashAlgorithm'));
        FormsAuthentication::setCipher($this->config->get('formsAuthentication.cipher'));
        FormsAuthentication::setEncryptionKey($this->config->get('formsAuthentication.encryptionKey'));
        FormsAuthentication::setValidationKey($this->config->get('formsAuthentication.validationKey')); 
    }
    
    /**
     * Override in global.php to bootstrap application.
     */
    public function load(){}

    /**
     * Override in global.php to provide custom authentication.
     */
    public function authenticateRequest(\System\Web\Mvc\Controller $controller){
        $httpAuthCookie = $this->httpContext->getRequest()->getCookies()->get(FormsAuthentication::getCookieName());
        
        $identity = new UserIdentity('Anonymous');
        
        if($httpAuthCookie){
            $ticket = FormsAuthentication::decrypt($httpAuthCookie->getValue()); 

            if((\System\Std\Date::now()->getTimestamp() < $ticket->getExpire()) || $ticket->getExpire()==0){
                $identity = new UserIdentity($ticket->getName(), $ticket->getUserData(), true);
            }
        }
        $this->httpContext->getRequest()->setUser($identity);
    }
    
    /**
     * Override in global.php
     * preAction event invoked before controller/action executed.
     */
    public function preAction(\System\Web\Mvc\Controller $controller){}
    
    /**
     * Override in global.php
     * postAction event invoked after controller/action executed.
     */
    public function postAction(\System\Web\Mvc\Controller $controller){}

    /**
     * Dispatches a controller/action.
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
                    $namespace = String::set(Environment::getControllerPath())->replace(Environment::getRootPath(), '')->trim('/');
                }

                $moduleName = $routeData->get('module') ? $routeData->get('module').'.' : '';
                $class = String::set(sprintf('%s.%sControllers.%sController', $namespace, ucfirst(strtolower($moduleName)), ucfirst(strtolower($routeData->get('controller')))))
                    ->replace('.', '\\');

                try{
                    $refClass = new \ReflectionClass((string)$class);
                    $controller = $refClass->newInstanceArgs(array());
                }catch(\ReflectionException $e){
                    throw new Mvc\ControllerNotFoundException(sprintf("The controller '%s' does not exist.", $class));
                }
                
                if(!$controller instanceof Mvc\Controller){
                    throw new Mvc\MvcException(sprintf("The controller '%s' does not inherit from System\Web\Mvc\Controller.", $class));
                }

                $moduleClassName = String::set(sprintf('%s.%sControllers.%s', $namespace, ucfirst($moduleName), 'Module'))->replace('.', '\\');

                $this->httpContext->getSession()->open();

                $this->authenticateRequest($controller);

                $this->preAction($controller);

                $moduleInstance = null;
                try{
                    $refModClass = new \ReflectionClass((string)$moduleClassName);
                    $moduleInstance = $refModClass->newInstanceArgs(array());
                }catch(\Exception $e){}

                if($moduleInstance){
                    if (method_exists($moduleInstance, 'load')){
                        $moduleInstance->load(new \System\Web\Mvc\ModuleContext($this->config, $controller, $this->httpContext));
                    }
                }

                $controller->getRegistry()->merge(get_object_vars($this));
                $controller->execute($this->httpContext);

                if($moduleInstance){
                    if (method_exists($moduleInstance, 'unload')){
                        $moduleInstance->unload(new \System\Web\Mvc\ModuleContext($this->config, $controller, $this->httpContext));
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
     * Override in global.php
     * Handle and log errors.
     */
    public function error(\Exception $e){}
    
    public function end(){
        $this->httpContext->getSession()->write();
        $this->httpContext->getResponse()->flush();
        exit;
    }
    
    /**
     * Configuration.
     *
     * @return System\Configuration\Configuration;
     */
    protected function setConfiguration(\System\Configuration\Configuration $config){
        $this->config = $config;
    }

    /**
     * Configuration.
     *
     * @return System\Configuration\Configuration;
     */
    protected function getConfiguration(){
        return $this->config;
    }
    
    /**
     * Logger.
     *
     * @return void
     */
    protected function setLogger(\System\Log\Logger $logger){
        $this->logger = $logger;
    }
    
    /**
     * Logger.
     *
     * @return System\Log\Logger;
     */
    protected function getLogger(){
        return $this->logger;
    }

    /**
     * Gets the HttpContext for the HTTP request.
     *
     * @return System\Web\HttpContext
     */
    protected function getHttpContext(){
        return $this->httpContext;
    }
    
    /**
     * Gets the route collection.
     *
     * @return System\Collections\Dictionary
     */
    protected function getRoutes(){
        return $this->routes;
    }
}