<?php

namespace System\Std;

final class Environment {
    private static $includes = array();
    private static $rootPath;
    private static $controllerPath;
    private static $executionTime;
    private static $namespaces = array();
    private static $cultureInfo;
    private static $dateTimeFormat;
    private static $timezone;
    
    /**
     * Adds an autoloaded file to the includes collection.
     * 
     * @method  addClassFile
     * @param   string $file
     * @return  void
     */
    public static function addClassFile($file){
        self::$includes[] = $file;
    }
    
    /**
     * Determines if a class file exists in the includes collection.
     * 
     * @method  hasClassFile
     * @param   string $file
     * @return  bool
     */
    public static function hasClassFile($file){
        if(in_array($file, self::$includes)){
            return true;
        }
        return false;
    }
    
    /**
     * Gets an array of all included files.
     * 
     * @method  getLoadedClassFiles
     * @return  array
     */
    public static function getLoadedClassFiles(){
        return self::$includes;
    }

    /**
     * Sets the application root path.
     * 
     * @method  setRootPath
     * @param   string $path
     * @return  void
     */
    public static function setRootPath($path){
        self::$rootPath = $path;
    }
    
    /**
     * Gets the application root path.
     * 
     * @method  getRootPath
     * @return  string
     */
    public static function getRootPath(){
        return self::$rootPath;
    }
    
    /**
     * Sets the controller path.
     * 
     * @method  setControllerPath
     * @param   string $path
     * @return  void
     */
    public static function setControllerPath($path){
        self::$controllerPath = $path;
    }
    
    /**
     * Gets the controller path.
     * 
     * @method  getControllerPath
     * @return  string
     */
    public static function getControllerPath(){
        return self::$controllerPath;
    }
    
    /**
     * Sets the execution time for the script.
     * 
     * @method  setExecutionTime
     * @param   int $seconds
     * @return  void
     */
    public static function setExecutionTime($seconds){
        set_time_limit($seconds);
        self::$executionTime = $seconds;
    }
    
    /**
     * Gets the execution time for the script.
     * 
     * @method  getExecutionTime
     * @return  int
     */
    public static function getExecutionTime(){
        return sef::$executionTime;
    }
    
    public static function setNamespaces(array $namespaces){
        self::$namespaces = $namespaces;
    }
    
    public static function getNamespaces(){
        return self::$namespaces;
    }
    
    /**
     * Sets the culture for the environment.
     * 
     * @method  setCulture
     * @param   System.Globalization.CultureInfo $cultureInfo
     * @return  void
     */
    public static function setCulture(\System\Globalization\CultureInfo $cultureInfo){
        self::$cultureInfo = $cultureInfo;
    }
    
    /**
     * Gets the culture for the environment.
     * 
     * @method  getCulture
     * @return  System.Globalization.CultureInfo
     */
    public static function getCulture(){
        return self::$cultureInfo;
    }
    
    /**
     * Sets the date time format for the environment.
     * 
     * @method  setDateTimeFormat
     * @param   string $format
     * @return  void
     */
    public static function setDateTimeFormat($format){
        self::$dateTimeFormat = $format;
    }
    
    /**
     * Gets the date time format for the environment.
     * 
     * @method  getDateTimeFormat
     * @return  string
     */
    public static function getDateTimeFormat(){
        return self::$dateTimeFormat;
    }
    
    /**
     * Sets the timezone for the environment.
     * 
     * @method  setTimezone
     * @param   string $timezone
     * @return  void
     */
    public static function setTimezone($timezone){
        self::$timezone = $timezone;
    }
    
    /**
     * Gets the timezone for the environment.
     * 
     * @method  getTimezone
     * @return  string
     */
    public static function getTimezone(){
        return self::$timezone;
    }
}