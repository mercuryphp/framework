<?php

namespace System\Globalization;

class Cultures {
    
    protected static $cultures = array();

    /**
     * Adds an instance of CultureInfo. 
     * 
     * @return  void
     */
    public static function add(CultureInfo $cultureInfo){
       static::$cultures['user'][] = $cultureInfo;
    }
    
    /**
     * Gets an array of cultures provided by the user.
     * 
     * @return  array
     */
    public static function getUserCultures(){
        return isset(static::$cultures['user']) ? static::$cultures['user'] : null;
    }
    
    /**
     * Gets an array of all cultures provided by the framework.
     * 
     * @return  array
     */
    public static function getInstalledCultures(){
        $cultures = array('installed' => array());
        $dir = \System\Std\Str::set(dirname(__FILE__).'/Data/');
        
        if (is_dir($dir)) {
            $dh = opendir($dir);
            if ($dh) {
                while (($file = readdir($dh)) !== false){
                    if(substr($file, 0,1) !='.'){
                        list($culture, $ext) = explode('.',$file, 2);
                        $cultures['installed'][] = new CultureInfo($culture);
                    }
                }
                closedir($dh);
                return $cultures['installed'];
            }
        }
        return array();
    }
    
    /**
     * Gets a combined array of installed and user cultures.
     * 
     * @return  array
     */
    public static function getAllCultures(){
        return array_merge(static::getInstalledCultures(), static::getUserCultures());
    }
}