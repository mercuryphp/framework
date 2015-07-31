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
    private static $defaultConnectionString;
    
    public static function addClassFile($file){
        self::$includes[] = $file;
    }
    
    public static function hasClassFile($file){
        if(in_array($file, self::$includes)){
            return true;
        }
        return false;
    }
    
    public static function getLoadedClassFiles(){
        return self::$includes;
    }

    public static function setRootPath($path){
        self::$rootPath = $path;
    }
    
    public static function getRootPath(){
        return self::$rootPath;
    }
    
    public static function setControllerPath($path){
        self::$controllerPath = $path;
    }
    
    public static function getControllerPath(){
        return self::$controllerPath;
    }
    
    public static function setExecutionTime($seconds){
        set_time_limit($seconds);
        self::$executionTime = $seconds;
    }
    
    public static function getExecutionTime(){
        return sef::$executionTime;
    }
    
    public static function setNamespaces(array $namespaces){
        self::$namespaces = $namespaces;
    }
    
    public static function getNamespaces(){
        return self::$namespaces;
    }
    
    public static function setCulture(\System\Globalization\CultureInfo $cultureInfo){
        self::$cultureInfo = $cultureInfo;
    }
    
    public static function getCulture(){
        return self::$cultureInfo;
    }
    
    public static function setDateTimeFormat($format){
        self::$dateTimeFormat = $format;
    }
    
    public static function getDateTimeFormat(){
        return self::$dateTimeFormat;
    }
    
    public static function setTimezone($timezone){
        self::$timezone = $timezone;
    }
    
    public static function getTimezone(){
        return self::$timezone;
    }
    
    public static function setDefaultConnectionString($connectionString){
        self::$defaultConnectionString = $connectionString;
    }
    
    public static function getDefaultConnectionString(){
        return self::$defaultConnectionString;
    }
}