<?php 

use System\Web\HttpApplication;
use System\Web\Routing\UriRoute;

class MvcApplication extends HttpApplication {

    public function load(){

        $this->routes->add('default', new UriRoute(
                array(
                    'controller' => 'Home', 
                    'action' => 'index'
                ),
                array('id')
            )
        );
    }
    
    public function error($e){
        print_r($e);
    }
}

?>