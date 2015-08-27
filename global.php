<?php 

use System\Web\HttpApplication;

class MvcApplication extends HttpApplication {

    public function load(){

        $this->routes->add('default', '{controller}/{action}/{id}',
            array(
                'controller' => 'Home', 
                'action' => 'index'
            ),
            array('id')
        );
    }
}