<?php

namespace System\Web;

use System\Std\Environment;
use System\Std\String;
use System\Std\Instance;
use System\Configuration\Configuration;
use System\Collections\Dictionary;
use System\Web\Mvc\NativeView;

class HttpApplication {

    protected $config;
    protected $httpContext;
    protected $routes;
    protected $view;
    
    public function __construct($rootPath){
        
        $this->routes = new Dictionary();
        $this->view = new NativeView();
        $this->config = new Configuration('config.php');
        
        Environment::setRootPath($rootPath);
        Environment::setAppPath($rootPath);
        Environment::setNamespaces($this->config->getNamespaces()->toArray());
        Environment::setCulture(new \System\Globalization\CultureInfo('en-GB'));

        $session = Instance::getInstance(
            $this->config->getSession()->getHandler(),
            array(
                $this->config->getSession()->getName(),
                $this->config->getSession()->getExpires(),
                $this->config->getSession()->getPath(),
                $this->config->getSession()->getDomain(),
                $this->config->getSession()->getSecure(),
                $this->config->getSession()->getHttpOnly()
            )
        );

        $request = new HttpRequest();
        $response = new HttpResponse();
        $this->httpContext = new HttpContext($request, $response, $session);
    }
    
    public function load(){}

    public function run(){
        
        if($this->routes->count() == 0){
            throw new \RuntimeException("test");
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
                    
                $controller->setViewEngine($this->view);

                $moduleClassName = String::set(sprintf('%s.%sControllers.%s', $namespace, ucfirst($moduleName), 'Module'))
                    ->replace('.', '\\');

                $this->preAction($controller);
                $moduleInstance = null;

                try{
                    $refModClass = new \ReflectionClass((string)$moduleClassName);
                    $moduleInstance = $refModClass->newInstanceArgs(array());
                }catch(\Exception $e){}

                if($moduleInstance != null){
                    if (method_exists($moduleInstance, 'load')){
                        $moduleInstance->load( new \System\Web\Mvc\ModuleContext($this->config, $controller, $this->httpContext));
                    }
                }

                $controller->execute($requestContext);
                
                $this->postAction($controller);
                
                if($moduleInstance != null){
                    if (method_exists($moduleInstance, 'unload')){
                        $moduleInstance->load( new \System\Web\Mvc\ModuleContext($this->config, $controller, $this->httpContext));
                    }
                }

                $this->httpContext->getSession()->writeSession();
                $this->httpContext->getResponse()->flush();
                break;
            }
        }
    }

    public function preAction(\System\Web\Mvc\Controller $controller){}
    
    public function postAction(\System\Web\Mvc\Controller $controller){}
    
    public function error(\Exception $e){}
}

?>
