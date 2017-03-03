<?php

namespace System\Web;

use System\Collections\Dictionary;

final class HttpRequest {

    private $uri;
    private $rawUri;
    private $queryString;
    private $uriSegments;
    private $routeData;
    private $server = array();
    private $cookies;
    private $input;
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
     * @param   string $uri = null
     */
    public function __construct($uri = null){
        $this->server = new Dictionary($_SERVER);
        $uri = $uri ? $uri : $this->getServer('REQUEST_URI');
        $this->rawUri = $uri;
        $this->input = file_get_contents("php://input");
        $pos = strpos($uri, '?');
        
        if((bool)$pos === true){
            $uri = substr($uri, 0, $pos);
            $this->queryString = trim(substr($this->rawUri, $pos+1), '/');
        }
        
        $inputParams = array();
        if($this->input){
            $inputParams = json_decode($this->input, true);
            if(!is_array($inputParams)){
                $inputParams = array();
            }
        }
             
        $this->uri = trim($uri, '/');
        $this->uriSegments = explode('/', $this->uri);
        $this->routeData = new Dictionary();
        $this->query = new Dictionary($_GET);
        $this->post = new Dictionary(array_merge($_POST, $inputParams));
        $this->cookies = new HttpCookieCollection($_COOKIE);
        $this->params = new Dictionary(array_merge($_REQUEST, $_COOKIE, $inputParams));
        $this->headers = (new Dictionary($_SERVER))->where(function($v, $k){ if (substr($k, 0,4)=='HTTP'){ return array($k => $v); }});
        $this->files = $this->httpFiles();
    }
    
    public function getRawInput(){
        return $this->input;
    }
    
    /**
     * Gets a cookie from the cookie collection. Creates a new cookie if the cookie does not exist.
     * Gets the cookie collection if no name is specified.
     * 
     * @param   string $name
     * @return  mixed
     */
    public function getCookies($name = null, $default = null){
        if($name){
            return $this->cookies->get($name, $default);
        }
        return $this->cookies;
    }
    
    /**
     * Gets the specified item by name or gets a collection of query 
     * items if no name is specified.
     * 
     * @param   string $name = null
     * @return  mixed
     */
    public function getQuery($name = null, $default = null){
        if($name){
            return $this->query->get($name, $default);
        }
        return $this->query;
    }
    
    /**
     * Gets the specified item by name or gets a collection of post 
     * items if no name is specified.
     * 
     * @param   string $name = null
     * @return  mixed
     */
    public function getPost($name = null, $default = null){
        if($name){
            return $this->post->get($name, $default);
        }
        return $this->post;
    }
    
    /**
     * Gets the specified HttpFile object by name or gets a collection of files 
     * if no name is specified.
     * 
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
     * @param   string $name
     * @param   mixed $value
     */
    public function setParam($name, $value){
        $this->params->set($name, $value);
    }
    
    /**
     * Gets the specified item by name or gets a combined collection of query, 
     * post, cookies, and server items if no name is specified.
     * 
     * @param   string $name = null
     * @return  mixed
     */
    public function getParam($name = null, $default = null){
        if($name){
            return $this->params->get($name, $default);
        }
        return $this->params;
    }
    
    /**
     * Gets the specified segment from the request URI by name or gets a collection of
     * segments if no name is specified.
     * 
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
     * @return  System.Collections.Dictionary
     */
    public function getRouteData(){
        return $this->routeData;
    }
    
    /**
     * Gets the request URI without query variables.
     * 
     * @return  string
     */
    public function getUri(){
        return $this->uri;
    }
    
    /**
     * Gets the request URI.
     * 
     * @return  string
     */
    public function getRawUri(){
        return $this->rawUri;
    }
    
    /**
     * Gets the request query string.
     * 
     * @return  string
     */
    public function getQueryString(){
        return $this->queryString;
    }
    
    /**
     * Gets the request user agent.
     * 
     * @return  string
     */
    public function getUserAgent(){
        return $this->getServer('HTTP_USER_AGENT');
    }
    
    /**
     * Gets the client address.
     * 
     * @return  string
     */
    public function getClientAddr(){
        return $this->getServer('REMOTE_ADDR');
    }
    
    /**
     * Gets the http protocol and hostname as a string.
     * 
     * @return  string
     */
    public function getHost(){
        $https = $this->getServer('HTTPS');
        
        if(!$https || $https == 'off'){
            return 'http://'.$this->getServer('HTTP_HOST');
        }else{
            return 'https://'.$this->getServer('HTTP_HOST');
        }
    }
    
    /**
     * Gets the Http method.
     * 
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
     * @return  System.Collections.Dictionary
     */
    public function getHeaders(){
        return $this->headers;
    }
    
    /**
     * Gets the specified server item by name.
     * 
     * @param   int $name
     * @return  string
     */
    public function getServer($name = null, $default = null){
        return $this->server->get($name, $default);
    }
    
    /**
     * Sets a UserIdentity object for the current request.
     * 
     * @param   System.Web.Security.UserIdentity $identity
     */
    public function setUser(\System\Web\Security\UserIdentity $identity){
        $this->user = $identity;
    }
    
    /**
     * Gets a UserIdentity object for the current request.
     * 
     * @return  System.Web.Security.UserIdentity
     */
    public function getUser(){
        return $this->user;
    }
    
    /**
     * Gets a boolean value indicating if the current request method is a GET.
     * 
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
     * @return  bool
     */
    public function isAjax(){
        if($this->getServer('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest'){
            return true;
        }
        return false;
    }
    
    public function hasKey($key){
        if ($this->params->hasKey($key)){
            return true;
        }
        return false;
    }

    /**
     * Binds request params to an object.
     * 
     * @param   object $object
     */
    public function bindModel($object){
        try{
            $refClass = new \ReflectionClass($object);
        }catch(\Exception $e){
            throw new \Exception(sprintf('Request binding failed. %s.', $e->getMessage()));
        }
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
     * @return  array
     */
    public function toArray(){
        return $this->params->toArray();
    }
    
    /**
     * Gets an item from the params collection using a dynamic property.
     * 
     * @return  mixed
     */
    public function __get($name){
        return $this->getParam($name);
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