<?php

namespace System\Web;

use System\Collections\Dictionary;

final class HttpRequest{

    private $uri;
    private $uriSegments;
    private $routeData;
    private $cookies;
    private $query = array();
    private $post = array();
    private $params = array();
    private $files = array();
    private $user;

    public function __construct($uri = null){
        
        $uri = $uri ? $uri : $this->getServer('REQUEST_URI');

        $pos = strpos($uri, '?');
        
        if((bool)$pos === true){
            $uri = substr($uri, 0, $pos);
        }

        $this->uri = trim($uri, '/');
        $this->uriSegments = explode('/', $this->uri);
        $this->routeData = new Dictionary();
        $this->query = new Dictionary($_GET);
        $this->post = new Dictionary($_POST);
        $this->params = new Dictionary($_REQUEST);
        $this->cookies = new HttpCookieCollection($_COOKIE);
        $this->files = $this->httpFiles();
    }
    
    public function getCookies(){
        return $this->cookies;
    }
    
    public function getQuery($field = null){
        if($field){
            return $this->query->get($field);
        }
        return $this->query;
    }
    
    public function getPost($field = null){
        if($field){
            return $this->post->get($field);
        }
        return $this->post;
    }
    
    public function getFile($field = null){
        if($field){
            return $this->files->get($field);
        }
        return $this->files;
    }
    
    public function setParam($field, $value){
        $this->params->set($field, $value);
    }
    
    public function getParam($field = null){
        if($field){
            return $this->params->get($field);
        }
        return $this->params;
    }
    
    public function getSegment($index = null){
        if($index>-1){
            if(isset($this->uriSegments[$index])){
                return $this->uriSegments[$index];
            }
        }
        return $this->uriSegments;
    }

    public function getRouteData(){
        return $this->routeData;
    }
    
    public function getUri(){
        return $this->uri;
    }
    
    public function getHttpMethod(){
        if($this->getServer('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest'){
            return 'AJAX';
        }
        return $this->getServer('REQUEST_METHOD');
    }
    
    public function setUser(\System\Web\Security\UserIdentity $identity){
        $this->user = $identity;
    }
    
    public function getUser(){
        return $this->user;
    }
    
    public function isGet(){
        if($this->getServer('REQUEST_METHOD') == 'GET'){
            return true;
        }
        return false;
    }
    
    public function isPost(){
        if($this->getServer('REQUEST_METHOD') == 'POST'){
            return true;
        }
        return false;
    }
    
    public function isPut(){
        if($this->getServer('REQUEST_METHOD') == 'PUT'){
            return true;
        }
        return false;
    }
    
    public function isDelete(){
        if($this->getServer('REQUEST_METHOD') == 'DELETE'){
            return true;
        }
        return false;
    }
    
    public function isAjax(){
        if($this->getServer('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest'){
            return true;
        }
        return false;
    }

    public function bindModel($object){
        $refClass = new \ReflectionClass($object);
        $properties = $refClass->getProperties();
        
        $params = $this->toArray();
        
        foreach($properties as $property){
            $property->setAccessible(true);
            $name = $property->getName();

            if (array_key_exists($name, $params)){
                $property->setValue($object, $params[$name]);
            }
        }
    }
    
    public function toArray(){
        return $this->params->toArray();
    }

    private function getServer($key){
        if(array_key_exists($key, $_SERVER)){
            return $_SERVER[$key];
        }
    }
    
    private function httpFiles(){
        
        $array = array();
        foreach($_FILES as $name=>$file){
            if(is_array($file['name'])){
                $count = count($file['name']);
                for($i=0; $i < $count; $i++){
                    if ($file['error'][$i] == UPLOAD_ERR_OK){
                        $array[$name.$i]['name'] =  $file['name'][$i];
                        $array[$name.$i]['type'] =  $file['type'][$i];
                        $array[$name.$i]['tmp_name'] =  $file['tmp_name'][$i];
                        $array[$name.$i]['size'] =  $file['size'][$i];
                    }
                }
            }else{
                if ($file['error'] == UPLOAD_ERR_OK){
                    $array[$name] = $file;
                }
            }
        }

        $list = new Dictionary();
        
        foreach($array as $key=>$item){
            $httpFile = new HttpFile();
            $httpFile->setFileName($item['name']);
            $httpFile->setTmpFileName($item['tmp_name']);
            $httpFile->setContentType($item['type']);
            $httpFile->setSize($item['size']);
            
            $list->add($key, $httpFile);
        }
        return $list;
    }
}

?>
