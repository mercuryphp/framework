<?php 

use System\Web\HttpApplication;

class MvcApplication extends HttpApplication {

    public function load(){
        $this->routes->add('{controller}/{action}/{id}', 
            array('controller' => 'Home', 'action' => 'index')
        );
    }
    
    public function error(\Exception $e) {
        $this->getLogger()->exception($e)->flush();
    }
}
