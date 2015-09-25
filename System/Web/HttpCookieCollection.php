<?php

namespace System\Web;

class HttpCookieCollection extends \System\Collections\Collection {
    
    /**
     * Initializes a new instance of HttpCookieCollection.
     * 
     * @param   array $cookies
     */
    public function __construct(array $cookies){
        foreach($cookies as $k=>$v){
            $cookie = new HttpCookie($k, $v);
            $this->collection[$k] = $cookie;
        }
    }
    
    /**
     * Adds a HttpCookie to the cookie collection.
     * 
     * @param   HttpCookie $httpCookie
     */
    public function add(HttpCookie $httpCookie){
        if(is_string($httpCookie->getName())){
            $this->collection[$httpCookie->getName()] = $httpCookie;
        }
    }
    
    /**
     * Gets a cookie from the cookie collection. Creates a new cookie if the cookie does not exist.
     * If the cookie does not exist but default has been specified, then the value of default is returned
     * and no cookie is created.
     * 
     * @param   mixed $key
     * @param   mixed $default = null
     * @return  mixed
     */
    public function get($key, $default = null){
        if($this->hasKey($key)){
            return $this->collection[$key];
        }else{
            if($default){
                return $default;
            }
        }
        $httpCookie = new HttpCookie($key);
        $this->collection[$key] = $httpCookie;
        return $httpCookie;
    }
}