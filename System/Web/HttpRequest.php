<?php

namespace System\Web;

use System\Collections\Dictionary;

final class HttpRequest {

    private $uri;
    private $uriSegments;
    private $routeData;
    private $cookies;
    private $query = array();
    private $post = array();
    private $params = array();
    private $headers = array();
    private $files = array();
    private $user;

    /**
     * Initializes a new instance of the HttpRequest class and encapsulates 
     * information about an individual HTTP request.
     * 
     * @method  __construct
     * @param   string $uri = null
     */
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
        $this->cookies = new HttpCookieCollection($_COOKIE);
        $this->params = new Dictionary(array_merge($_REQUEST, $_COOKIE, $_SERVER));
        $this->headers = (new Dictionary($_SERVER))->where(function($v, $k){ if (substr($k, 0,4)=='HTTP'){ return array($k => $v); }});
        $this->files = $this->httpFiles();
    }
    
    /**
     * Gets the specified item by name or gets the cookie collection
     * if no name is specified.
     * 
     * @method  getCookies
     * @param   string $name = null
     * @return  mixed
     */
    public function getCookies($name = null){
        if($name){
            return $this->cookies->get($name);
        }
        return $this->cookies;
    }
    
    /**
     * Gets the specified item by name or gets a collection of query 
     * items if no name is specified.
     * 
     * @method  getQuery
     * @param   string $name = null
     * @return  mixed
     */
    public function getQuery($name = null){
        if($name){
            return $this->query->get($name);
        }
        return $this->query;
    }
    
    /**
     * Gets the specified item by name or gets a collection of post 
     * items if no name is specified.
     * 
     * @method  getPost
     * @param   string $name = null
     * @return  mixed
     */
    public function getPost($name = null){
        if($name){
            return $this->post->get($name);
        }
        return $this->post;
    }
    
    /**
     * Gets the specified HttpFile object by name or gets a collection of files 
     * if no name is specified.
     * 
     * @method  getFile
     * @param   string $name = null
     * @return  mixed
     */
    public function getFile($name = null){
        if($name){
            return $this->files->get($name);
        }
        return $this->files;
    }
    
    /**
     * Sets an item in the params collection.
     * 
     * @method  setParam
     * @param   string $name
     * @param   string $value
     */
    public function setParam($name, $value){
        $this->params->set($name, $value);
    }
    
    /**
     * Gets the specified item by name or gets a combined collection of query, 
     * post, cookies, and server items if no name is specified.
     * 
     * @method  getParam
     * @param   string $name = null
     * @return  mixed
     */
    public function getParam($name = null){
        if($name){
            return $this->params->get($name);
        }
        return $this->params;
    }
    
    /**
     * Gets the specified segment from the request URI by name or gets a collection of
     * segments if no name is specified.
     * 
     * @method  getSegment
     * @param   int $index
     * @return  string
     */
    public function getSegment($index = null){
        if($index>-1){
            if(isset($this->uriSegments[$index])){
                return $this->uriSegments[$index];
            }
        }
        return $this->uriSegments;
    }

    /**
     * Gets a collection of route data.
     * 
     * @method  getSegment
     * @return  System.Collections.Dictionary
     */
    public function getRouteData(){
        return $this->routeData;
    }
    
    /**
     * Gets the request URI.
     * 
     * @method  getUri
     * @return  string
     */
    public function getUri(){
        return $this->uri;
    }
    
    /**
     * Gets the request user agent.
     * 
     * @method  getUserAgent
     * @return  string
     */
    public function getUserAgent(){
        return $this->getServer('HTTP_USER_AGENT');
    }
    
    /**
     * Gets the client address.
     * 
     * @method  getClientAddr
     * @return  string
     */
    public function getClientAddr(){
        return $this->getServer('REMOTE_ADDR');
    }
    
    /**
     * Gets the Http method.
     * 
     * @method  getHttpMethod
     * @return  string
     */
    public function getHttpMethod(){
        if($this->getServer('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest'){
            return 'AJAX';
        }
        return $this->getServer('REQUEST_METHOD');
    }
    
    /**
     * Gets a collection of request headers.
     * 
     * @method  getHeaders
     * @return  System.Collections.Dictionary
     */
    public function getHeaders(){
        return $this->headers;
    }
    
    /**
     * Gets the specified server item by name.
     * 
     * @method  getServer
     * @param   int $name
     * @return  string
     */
    public function getServer($name){
        if(array_key_exists($name, $_SERVER)){
            return $_SERVER[$name];
        }
    }
    
    /**
     * Sets a UserIdentity object for the current request.
     * 
     * @method  setUser
     * @param   System.Web.Security.UserIdentity $identity
     */
    public function setUser(\System\Web\Security\UserIdentity $identity){
        $this->user = $identity;
    }
    
    /**
     * Gets a UserIdentity object for the current request.
     * 
     * @method  getUser
     * @return  System.Web.Security.UserIdentity
     */
    public function getUser(){
        return $this->user;
    }
    
    /**
     * Gets a boolean value indicating if the current request method is a GET.
     * 
     * @method  isGet
     * @return  bool
     */
    public function isGet(){
        if($this->getServer('REQUEST_METHOD') == 'GET'){
            return true;
        }
        return false;
    }
    
    /**
     * Gets a boolean value indicating if the current request method is a POST.
     * 
     * @method  isPost
     * @return  bool
     */
    public function isPost(){
        if($this->getServer('REQUEST_METHOD') == 'POST'){
            return true;
        }
        return false;
    }
    
    /**
     * Gets a boolean value indicating if the current request method is a PUT.
     * 
     * @method  isPut
     * @return  bool
     */
    public function isPut(){
        if($this->getServer('REQUEST_METHOD') == 'PUT'){
            return true;
        }
        return false;
    }
    
    /**
     * Gets a boolean value indicating if the current request method is a DELETE.
     * 
     * @method  isDelete
     * @return  bool
     */
    public function isDelete(){
        if($this->getServer('REQUEST_METHOD') == 'DELETE'){
            return true;
        }
        return false;
    }
    
    /**
     * Gets a boolean value indicating if the current request was requested with 
     * XMLHttpRequest.
     * 
     * @method  isAjax
     * @return  bool
     */
    public function isAjax(){
        if($this->getServer('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest'){
            return true;
        }
        return false;
    }

    /**
     * Binds request params to an object.
     * 
     * @method  bindModel
     * @param   object $object
     */
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
    
    /**
     * Gets a PHP array of request params.
     * 
     * @method  toArray
     * @return  array
     */
    public function toArray(){
        return $this->params->toArray();
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
            $list->add($key, new HttpFile($item['name'], $item['tmp_name'], $item['type'], $item['size']));
        }
        return $list;
    }
}