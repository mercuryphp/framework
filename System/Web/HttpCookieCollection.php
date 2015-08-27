<?php

namespace System\Web;

class HttpCookieCollection extends \System\Collections\Collection {
    
    /**
     * Initializes the cookie collection.
     * 
     * @method  __construct
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
     * @method  add
     * @param   HttpCookie $httpCookie
     */
    public function add(HttpCookie $httpCookie){
        if(is_string($httpCookie->getName())){
            $this->collection[$httpCookie->getName()] = $httpCookie;
        }
    }
    
    /**
     * Removes a HttpCookie from the cookie collection using the cookie name.
     * 
     * @method  remove
     * @param   string $key
     */
    public function remove($key){
        $this->readOnlyCheck();
        if($this->hasKey($key)){
            $httpCookie = $this->collection[$key];
            $httpCookie->setExpires(\System\Std\Date::now()->sub(new \DateInterval('P1Y')));
            return true;
        }
        return false;
    }
    
    /**
     * Clears the cookie collection.
     * 
     * @method  clear
     */
    public function clear(){
        $this->readOnlyCheck();
        foreach($this->collection as $httpCookie){
            $httpCookie->setExpires(\System\Std\Date::now()->sub(new \DateInterval('P1Y')));
        }
    }
}