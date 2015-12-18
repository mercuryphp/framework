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
     * @param   string $name
     * @return  void
     */
    public function setName($name){
        $this->name = $name;
        return $this;
    }
    
    /**
     * Gets the cookie name.
     * 
     * @return  string
     */
    public function getName(){
        return $this->name;
    }
    
    /**
     * Sets the cookie value.
     * 
     * @param   string $value
     * @return  void
     */
    public function setValue($value){
        $this->value = $value;
        return $this;
    }
    
    /**
     * Gets the cookie value.
     * 
     * @return  mixed
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * Sets the expiration date and time for the cookie.
     * 
     * @param   int $expires
     * @return  void
     */
    public function setExpires($expires){
        $this->expires = $expires;
        return $this;
    }
    
    /**
     * Gets the expiration date and time for the cookie.
     * 
     * @return  string
     */
    public function getExpires(){
        return $this->expires;
    }
    
    /**
     * Sets the path on the server where the cookie will be available.
     * 
     * @param   string $path
     * @return  void
     */
    public function setPath($path){
        $this->path = $path;
        return $this;
    }
    
    /**
     * Gets the path on the server where the cookie will be available.
     * 
     * @return  string
     */
    public function getPath(){
        return $this->path;
    }
    
    /**
     * Sets the domain to associate the cookie with.
     * 
     * @param   string $domain
     * @return  void
     */
    public function setDomain($domain){
        $this->domain = $domain;
        return $this;
    }
    
    /**
     * Gets the domain to associate the cookie with.
     * 
     * @return  string
     */
    public function getDomain(){
        return $this->domain;
    }

    /**
     * Sets a value indicating that the cookie should only be transmitted 
     * over a secure HTTPS connection from the client.
     * 
     * @param   bool $bool = null
     * @return  bool
     */
    public function setIsSecure($bool = null){
        $this->isSecure = $bool;
        return $this;
    }
    
    /**
     * Gets a value indicating that the cookie should only be transmitted 
     * over a secure HTTPS connection from the client.
     * 
     * @param   bool $bool = null
     * @return  bool
     */
    public function getIsSecure(){
        return $this->isSecure;
    }

    /**
     * Sets a value indicating that the cookie should be accessible only 
     * through the HTTP protocol.
     * 
     * @param   bool $bool = null
     * @return  bool
     */
    public function setIsHttpOnly($bool = null){
        $this->isHttpOnly = $bool;
        return $this;
    }
    
    /**
     * Gets a value indicating that the cookie should be accessible only 
     * through the HTTP protocol.
     * 
     * @param   bool $bool = null
     * @return  bool
     */
    public function getIsHttpOnly(){
        return $this->isHttpOnly;
    }
}