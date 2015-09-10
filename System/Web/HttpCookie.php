<?php

namespace System\Web;

final class HttpCookie {

    private $name;
    private $value;
    private $expires;
    private $path;
    private $domain;
    private $isSecure;
    private $isHttpOnly;
    
    /**
     * Initializes a new instance of HttpCookie.
     * 
     * @method  __construct
     * @param   string $name
     * @param   string $value = ''
     * @param   mixed $expires = 0
     * @param   string $path = '/'
     * @param   string $domain = ''
     * @param   bool $isSecure = false
     * @param   bool $isHttpOnly = true
     * 
     */
    public function __construct($name, $value = '', $expires = 0, $path = '/', $domain = '', $isSecure = false, $isHttpOnly = true){
        $this->name = $name;
        $this->value = $value;
        $this->expires = $expires;
        $this->path = $path;
        $this->domain = $domain;
        $this->isSecure = $isSecure;
        $this->isHttpOnly = $isHttpOnly;
    }

    /**
     * Sets the cookie name.
     * 
     * @method  setName
     * @param   string $name
     * @return  void
     */
    public function setName($name){
        $this->name = $name;
    }
    
    /**
     * Gets the cookie name.
     * 
     * @method  getName
     * @return  string
     */
    public function getName(){
        return $this->name;
    }
    
    /**
     * Sets the cookie value.
     * 
     * @method  setValue
     * @param   string $value
     * @return  void
     */
    public function setValue($value){
        $this->value = $value;
    }
    
    /**
     * Gets the cookie value.
     * 
     * @method  getValue
     * @return  mixed
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * Sets the expiration date and time for the cookie.
     * 
     * @method  setExpires
     * @param   string $expires
     * @return  void
     */
    public function setExpires($expires){
        $this->expires = $expires;
    }
    
    /**
     * Gets the expiration date and time for the cookie.
     * 
     * @method  getExpires
     * @return  string
     */
    public function getExpires(){
        return $this->expires;
    }
    
    /**
     * Sets the path on the server where the cookie will be available.
     * 
     * @method  setPath
     * @param   string $path
     * @return  void
     */
    public function setPath($path){
        $this->path = $path;
    }
    
    /**
     * Gets the path on the server where the cookie will be available.
     * 
     * @method  getPath
     * @return  string
     */
    public function getPath(){
        return $this->path;
    }
    
    /**
     * Sets the domain to associate the cookie with.
     * 
     * @method  setDomain
     * @param   string $domain
     * @return  void
     */
    public function setDomain($domain){
        $this->domain = $domain;
    }
    
    /**
     * Gets the domain to associate the cookie with.
     * 
     * @method  getDomain
     * @return  string
     */
    public function getDomain(){
        return $this->domain;
    }

    /**
     * Gets or sets a value indicating that the cookie should only be transmitted 
     * over a secure HTTPS connection from the client.
     * 
     * @method  isSecure
     * @param   bool $bool = null
     * @return  bool
     */
    public function isSecure($bool = null){
        if(!is_null($bool)){
            $this->isSecure = $bool;
        }else{
            return $this->isSecure;
        }
    }

    /**
     * Gets or sets a value indicating that the cookie should be accessible only 
     * through the HTTP protocol.
     * 
     * @method  isHttpOnly
     * @param   bool $bool = null
     * @return  bool
     */
    public function isHttpOnly($bool = null){
        if(!is_null($bool)){
            $this->isHttpOnly = $bool;
        }else{
            return $this->isHttpOnly;
        }
    }
}