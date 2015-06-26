<?php 

use System\Web\HttpApplication;
use System\Web\Routing\UriRoute;

class MvcApplication extends HttpApplication {

    public function load(){

        $this->routes->add('default', new UriRoute(
                array(
                    'controller' => 'Index', 
                    'action' => 'index'
                ),
                array('id')
            )
        );
    }
}

?>