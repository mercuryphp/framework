<?php

namespace System\Web;

use System\Std\Environment;
use System\Std\String;
use System\Std\Object;
use System\Globalization\CultureInfo;
use System\Configuration\Configuration;
use System\Collections\Dictionary;
use System\Web\Security\FormsAuthentication;
use System\Web\Security\UserIdentity;
use System\Web\Mvc\NativeView;

/**
 * HttpApplication
 * 
 * Executes and dispatches controller/action
 *
 * @license http://www.mercuryphp.com/license
 */
abstract class HttpApplication {

    /**
     * Application configuration
     *
     * @var System\Configuration\Configuration
     */
    protected $config;
    
    /**
     * Encapsulates Http request/response/server details
     *
     * @var System\Web\HttpContext
     */
    protected $httpContext;
    
    /**
     * Collection of routes
     *
     * @var System\Collections\Dictionary.
     */
    protected $routes;
    
    /**
     * Default View
     *
     * @var System\Web\Mvc\NativeView
     */
    protected $view;
    
    /**
     * Instantiates the application configuration object.
     */
    public function initConfiguration(){
        $this->config = new Configuration('config.php'); 
    }
    
    /**
     * Initializes application settings.
     */
    public function init($rootPath){
        $this->routes = new Dictionary();
        $this->view = new NativeView();

        if(!$this->config instanceof \System\Configuration\Configuration){
            throw new \RuntimeException('Configuration file has not been initialized');
        }

        Environment::setRootPath($rootPath);
        Environment::setAppPath($rootPath);
        Environment::setExecutionTime($this->config->getEnvironment()->getExecutionTime());
        Environment::setNamespaces($this->config->getNamespaces()->toArray());
        Environment::setCulture(new CultureInfo($this->config->getEnvironment()->getLocale()));
        Environment::setDateTimeFormat($this->config->getEnvironment()->getDateTimeFormat());
        Environment::setTimezone($this->config->getEnvironment()->getTimezone());
        Environment::setDefaultConnectionString($this->config->getConnectionStrings()->get('default'));

        $request = new HttpRequest();
        $response = new HttpResponse();
        
        $session = Object::getInstance(
            $this->config->getSession()->getHandler(),
            array(
                $request,
                $response,
                $this->config->getSession()->getName(),
                $this->config->getSession()->getExpires(),
                $this->config->getSession()->getPath(),
                $this->config->getSession()->getDomain(),
                $this->config->getSession()->getSecure(),
                $this->config->getSession()->getHttpOnly()
            )
        );

        $this->httpContext = new HttpContext($request, $response, $session);
        
        FormsAuthentication::setCookieName($this->config->getFormsAuthentication()->getCookieName());
        FormsAuthentication::setEncryptionKey($this->config->getFormsAuthentication()->getEncryptionKey());
        FormsAuthentication::setValidationKey($this->config->getFormsAuthentication()->getValidationKey());
    }
    
    /**
     * Override in global.php to bootstrap application.
     */
    public function load(){}

    public function authenticateRequest(\System\Web\Mvc\Controller $controller){
        $httpAuthCookie = $this->httpContext->getRequest()->getCookies()->get(FormsAuthentication::getCookieName());
        
        if($httpAuthCookie){
            $ticket = FormsAuthentication::decrypt($httpAuthCookie->getValue());
            $identity = new UserIdentity($ticket->getName(), $ticket->getUserData(), true);
        }else{
            $identity = new UserIdentity('Anonymous');
        }
        
        $controller->setUser($identity);
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
    public function run(){
        
        if($this->routes->count() == 0){
            throw new \RuntimeException('One or more routes must be registered.');
        }

        foreach($this->routes as $route){
            $route->setHttpContext($this->httpContext);
            
            $requestContext = $route->execute();

            if($requestContext){
                $routeData = $requestContext->getRouteData();
                
                $namespace = '';
                if(Environment::getRootPath() != Environment::getAppPath()){
                    $namespace = String::set(Environment::getAppPath())->replace(Environment::getRootPath(), "")->trim("/");
                }

                $moduleName = $routeData->get('module') ? $routeData->get('module').'.' : '';
                $class = String::set(sprintf('%s.%sControllers.%sController', $namespace, ucfirst($moduleName), ucfirst($routeData->get('controller'))))
                        ->replace('.', '\\');

                try{
                    $refClass = new \ReflectionClass((string)$class);
                    $controller = $refClass->newInstanceArgs(array());
                }catch(\ReflectionException $e){
                    throw new Mvc\ControllerNotFoundException(sprintf("The controller '%s' does not exist", $class));
                }
                
                if(!$controller instanceof Mvc\Controller){
                    throw new Mvc\MvcException(sprintf("The controller '%s' does not inherit from System\Web\Mvc\Controller", $class));
                }
                    
                $controller->setViewEngine($this->view);

                $moduleClassName = String::set(sprintf('%s.%sControllers.%s', $namespace, ucfirst($moduleName), 'Module'))
                    ->replace('.', '\\');

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

                $controller->execute($requestContext);

                if($moduleInstance){
                    if (method_exists($moduleInstance, 'unload')){
                        $moduleInstance->unload(new \System\Web\Mvc\ModuleContext($this->config, $controller, $this->httpContext));
                    }
                }
                
                $this->postAction($controller);

                $this->httpContext->getSession()->writeSession();
                $this->httpContext->getResponse()->flush();
                break;
            }
        }
    }
}

?>
