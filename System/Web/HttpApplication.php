<?php

namespace System\Web;

use System\Std\Environment;
use System\Std\String;
use System\Std\Object;
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
     * Application configuration.
     *
     * @var System\Configuration\Configuration
     */
    private $config = null;
    
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

    /**
     * Instantiates the application configuration object.
     */
    public function initConfiguration(){
        $this->config = new Configuration(new \System\Configuration\Readers\YamlReader('config.php')); 
    }
    
    /**
     * Initializes application settings.
     */
    public final function init($rootPath){

        $this->routes = new RouteCollection();

        Environment::setRootPath($rootPath);
        Environment::setControllerPath($rootPath);
        Environment::setExecutionTime($this->config->getEnvironment()->getExecutionTime());
        Environment::setNamespaces($this->config->getNamespaces()->toArray());
        Environment::setCulture(new CultureInfo($this->config->getEnvironment()->getLocale()));
        Environment::setDateTimeFormat($this->config->getEnvironment()->getDateTimeFormat());
        Environment::setTimezone($this->config->getEnvironment()->getTimezone());
        Environment::setDefaultConnectionString($this->config->getConnectionStrings()->get('default'));

        $request = new HttpRequest();
        $response = new HttpResponse();

        $session = Object::getInstance($this->config->getSession()->getHandler(),array($request,$response));
        $session->setName($this->config->getSession()->getName());
        $session->setExpires($this->config->getSession()->getExpires());
        $session->setPath($this->config->getSession()->getPath());
        $session->setDomain($this->config->getSession()->getDomain());
        $session->isSecure($this->config->getSession()->isSecure());
        $session->isHttpOnly($this->config->getSession()->isHttpOnly());

        $this->httpContext = new HttpContext($request, $response, $session);
        
        FormsAuthentication::setCookieName($this->config->getFormsAuthentication()->getCookieName());
        FormsAuthentication::setHashAlgorithm($this->config->getFormsAuthentication()->getHashAlgorithm());
        FormsAuthentication::setCipher($this->config->getFormsAuthentication()->getCipher());
        FormsAuthentication::setEncryptionKey($this->config->getFormsAuthentication()->getEncryptionKey());
        FormsAuthentication::setValidationKey($this->config->getFormsAuthentication()->getValidationKey()); 
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

            if(\System\Std\Date::now()->getTimestamp() < $ticket->getExpire()){
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
     * Override in global.php
     * Handle and log errors.
     */
    public function error(\Exception $e){}
    
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
    
    public function end(){
        $this->httpContext->getSession()->write();
        $this->httpContext->getResponse()->flush();
        exit;
    }
}