<?php

    if(!ob_get_level()){ 
        ob_start(); 
    }

    $rootPath = str_replace('\\', '/', dirname(__FILE__));
    $envClassFile = $rootPath .'/System/Std/Loader.php';
    //require $envClassFile;
    
    //\System\Std\Environment::addClassFile($envClassFile);
    
    if(is_file('autoload.php')){
        require autoload.php;
    }
    
    exit;
    spl_autoload_register(function($class){
        //System\Std\Loader::load(dirname(__FILE__), $class);
    });
    
    set_error_handler(function($errno, $errstr, $errfile, $errline){
        throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
    });

    function classFile($path, $class){
        $namespacePath = str_replace(array('\\', '.'), '/', $class);
        $file = str_replace('\\', '/', $path) . '/' . $namespacePath . '.php';
        return $file;
    }

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