<?php

    if(!ob_get_level()){ 
        ob_start(); 
    }

    $rootPath = str_replace('\\', '/', dirname(__FILE__));

    spl_autoload_register(function($class) use($rootPath){
        $file = $rootPath .'/'.str_replace('\\', '/', $class).'.php';

        if (is_file($file)){
            require $file;
        }
    });
    
    if(is_file('autoload.php')){
        require $rootPath.'/autoload.php';
        
        if(isset($autoloaders) && is_array($autoloaders)){
            foreach($autoloaders as $loader){
                spl_autoload_register($loader);
            }
        }
    }

    set_error_handler(function($errno, $errstr, $errfile, $errline){
        throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
    });

    require $rootPath . '/global.php';

    $mvcApplication = new MvcApplication($rootPath);
    
    try {
        $mvcApplication->start();
        $mvcApplication->init();
        $mvcApplication->load();
        $mvcApplication->run();
    }catch(\Exception $e){
        $mvcApplication->error($e);
    }
    $mvcApplication->end();
?>