<?php

namespace System\Web;

class HttpCookieCollection implements \IteratorAggregate{
    
    private $cookies = array();
    
    public function __construct($cookies){
        foreach($cookies as $k=>$v){
            $cookie = new HttpCookie($k, $v);
            $this->cookies[$k] = $cookie;
        }
    }
    
    public function add(HttpCookie $httpCookie){
        $this->cookies[$httpCookie->getName()] = $httpCookie;
    }
    
    public function clear(){
        foreach($this->cookies as $httpCookie){
            $httpCookie->setExpires(\System\Std\DateTime::now()->sub(new \DateInterval('P1Y')));
        }
    }
    
    public function get($index){
        $keys = array_keys($this->cookies);
        
        if(isset($keys[$index])){
            $key = $keys[$index];
            return $this->cookies[$key];
        }
    }
    
    public function count(){
        return count($this->cookies);
    }
    
    public function getIterator(){
        return new \ArrayIterator($this->cookies);
    }
}

?>
