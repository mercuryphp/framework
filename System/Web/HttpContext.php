<?php

namespace System\Web;

class HttpContext {
    
    private $request;
    private $response;
    private $session;
    
    /**
     * Initializes a new instance of the HttpContext class that encapsulates 
     * information about an HTTP request.
     * 
     * @method  __construct
     * @param   System.Web.HttpRequest $request
     * @param   System.Web.HttpResponse $response
     * @param   System.Web.Session.Session $session
     */
    public function __construct(HttpRequest $request, HttpResponse $response, Session\Session $session){
        $this->request = $request;
        $this->response = $response;
        $this->session = $session;
    }
    
    /**
     * Sets an HttpRequest object.
     * 
     * @method  setRequest
     * @param   System.Web.HttpRequest $request
     */
    public function setRequest(HttpRequest $request){
        $this->request = $request;
    } 

    /**
     * Gets the HttpRequest object for this context.
     * 
     * @method  getRequest
     * @return  System.Web.HttpRequest
     */
    public function getRequest(){
        return $this->request;
    } 
    
    /**
     * Sets an HttpResponse object.
     * 
     * @method  setResponse
     * @param   System.Web.HttpResponse $response
     */
    public function setResponse(HttpResponse $response){
        $this->response = $response;
    }
    
    /**
     * Gets the HttpResponse object for this context.
     * 
     * @method  getResponse
     * @return  System.Web.HttpResponse
     */
    public function getResponse(){
        return $this->response;
    }  
    
    /**
     * Sets the Session store for this context.
     * 
     * @method  setSession
     * @param   System.Web.Session.Session $session
     */
    public function setSession(Session\Session $session){
        $this->session = $session;
    }
    
    /**
     * Gets the Session store object for this context.
     * 
     * @method  getSession
     * @return  System.Web.Session.Session
     */
    public function getSession(){
        return $this->session;
    } 
}