<?php

namespace System\Web\Routing;

class UriRoute extends Route{
    
    protected $routeParams = array();
    protected $params = array();
        
    public function __construct(array $routeParams = array(), $params = array()){
        $this->routeParams = $routeParams;
        $this->params = array_reverse($params);
    }
   
    public function execute(){
        $uri = $this->httpContext->getRequest()->getUri();

        $routeData = array(
            'module' => '' ,
            'controller' => $this->routeParams['controller'],
            'action' => $this->routeParams['action']
        );
        
        if($uri){
            $segments = explode('/', $uri);
            $idx=0;

            if(array_key_exists('module', $this->routeParams)){ 
                $modules = array_map('strtolower',$this->routeParams['module']);
                if(is_array($modules)){
                    if(in_array($segments[$idx], $modules)){
                        $routeData['module'] = strtolower($segments[$idx]);
                        $idx++;
                    }
                }
            }

            if(isset($segments[$idx])){
                $routeData['controller'] = $segments[$idx];
                $idx++;
            }

            if(isset($segments[$idx])){
                $routeData['action'] = $segments[$idx];
                $idx++;
            }

            for($i=$idx; $i < count($segments); $i++){
                $paramKey = array_pop($this->params);
                if($paramKey){
                    $this->httpContext->getRequest()->setParam($paramKey, $segments[$i]);
                }
            }
        }

        return new RequestContext($this->httpContext, $routeData);
    }
}

?>