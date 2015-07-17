<?php

namespace System\Web;

class HttpCookieCollection extends \System\Collections\Collection {
    
    public function __construct($cookies){
        foreach($cookies as $k=>$v){
            $cookie = new HttpCookie($k, $v);
            $this->collection[$k] = $cookie;
        }
    }
    
    public function add(HttpCookie $httpCookie){
        if(is_string($httpCookie->getName())){
            $this->collection[$httpCookie->getName()] = $httpCookie;
        }
    }
    
    public function remove($key){
        $this->readOnlyCheck();
        if($this->hasKey($key)){
            $httpCookie = $this->collection[$key];
            $httpCookie->setExpires(\System\Std\Date::now()->sub(new \DateInterval('P1Y')));
            return true;
        }
        return false;
    }
    
    public function clear(){
        $this->readOnlyCheck();
        foreach($this->collection as $httpCookie){
            $httpCookie->setExpires(\System\Std\Date::now()->sub(new \DateInterval('P1Y')));
        }
    }
}

?>
