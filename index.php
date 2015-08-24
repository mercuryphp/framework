<?php

    $rootPath = str_replace('\\', '/', dirname(__FILE__));

    $envClassFile = $rootPath .'/System/Std/Environment.php';
    
    require $envClassFile;
    
    \System\Std\Environment::addClassFile($envClassFile);
    
    set_error_handler(function($errno, $errstr, $errfile, $errline){
        throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
    });

    spl_autoload_register(function($class){

        $path = str_replace('\\', '/', dirname(__FILE__));
        $file = classFile($path, $class);

        if (is_file($file)){
            \System\Std\Environment::addClassFile($file);
            require $file;
        }else{
            $namespaces = \System\Std\Environment::getNamespaces();
            $namespaceClass = ''; 
            
            $segments = explode('\\',$class);
            $class = array_pop($segments);
            
            if(isset($namespaces[$class])){
                $namespaceClass = $namespaces[$class];
            }
            $file = classFile($path, $namespaceClass);

            if(is_file($file)){ 
                if(!\System\Std\Environment::hasClassFile($file)){
                    require $file;
                }
                class_alias(str_replace('.', '\\', $namespaceClass), $class);
            }
        }
    });

    function classFile($path, $class){
        $namespacePath = str_replace(array('\\', '.'), '/', $class);
        $file = $path . '/' . $namespacePath . '.php';
        return $file;
    }

    require $rootPath . '/global.php';

    $mvcApplication = new MvcApplication($rootPath);
    
    try {
        $mvcApplication->start();
        $mvcApplication->init();
        $mvcApplication->load();
        $mvcApplication->run();
        $mvcApplication->end();
    }catch(\Exception $e){
        $mvcApplication->error($e);
    }
 
?>