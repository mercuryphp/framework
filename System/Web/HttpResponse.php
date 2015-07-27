<?php

namespace System\Web;

final class HttpResponse {

    private $headers;
    private $cookies;
    private $redirect;
    private $output;
    private $statusCode;
    private $statusCodes = array(
        //Informational 1xx
        100 => '100 Continue',
        101 => '101 Switching Protocols',
        //Successful 2xx
        200 => '200 OK',
        201 => '201 Created',
        202 => '202 Accepted',
        203 => '203 Non-Authoritative Information',
        204 => '204 No Content',
        205 => '205 Reset Content',
        206 => '206 Partial Content',
        //Redirection 3xx
        300 => '300 Multiple Choices',
        301 => '301 Moved Permanently',
        302 => '302 Found',
        303 => '303 See Other',
        304 => '304 Not Modified',
        305 => '305 Use Proxy',
        306 => '306 (Unused)',
        307 => '307 Temporary Redirect',
        //Client Error 4xx
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        402 => '402 Payment Required',
        403 => '403 Forbidden',
        404 => '404 Not Found',
        405 => '405 Method Not Allowed',
        406 => '406 Not Acceptable',
        407 => '407 Proxy Authentication Required',
        408 => '408 Request Timeout',
        409 => '409 Conflict',
        410 => '410 Gone',
        411 => '411 Length Required',
        412 => '412 Precondition Failed',
        413 => '413 Request Entity Too Large',
        414 => '414 Request-URI Too Long',
        415 => '415 Unsupported Media Type',
        416 => '416 Requested Range Not Satisfiable',
        417 => '417 Expectation Failed',
        422 => '422 Unprocessable Entity',
        423 => '423 Locked',
        //Server Error 5xx
        500 => '500 Internal Server Error',
        501 => '501 Not Implemented',
        502 => '502 Bad Gateway',
        503 => '503 Service Unavailable',
        504 => '504 Gateway Timeout',
        505 => '505 HTTP Version Not Supported'
    );
    
    public function __construct(){
        $this->cookies = new HttpCookieCollection(array());
        $this->headers = new \System\Collections\Dictionary();
    }

    public function addHeader($header, $value, $override = true){
        if($override){
            $this->headers->set($header, $value);
        }else{
            if(!$this->headers->hasKey($header)){
                $this->headers->add($header, $value);
            }
        }
        return $this;
    }
    
    public function write($output){
        $this->output = $output;
        return $this;
    }
    
    public function writeFile($file){
        if(is_file($file)){
            $this->output = file_get_contents($file);
        }
        return $this;
    }
    
    public function redirect($location, $immediateRedirect = true){
        if(is_string($location)){
            if($immediateRedirect){
                header('Location: ' . $location);
                exit;
            }
            $this->redirect = $location;
        }
    }

    public function setStatusCode($code){
        if (isset($this->statusCodes[$code])){
            $this->statusCode = $code;
            $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
            header($protocol.' '.$this->statusCodes[$code]);
        }
        return $this;
    }
    
    public function getStatusCode(){
        return $this->statusCode;
    }
    
    public function getCookies(){
        return $this->cookies;
    }
    
    public function getOutput(){
        return $this->output;
    }

    public function flush(){
        if (!headers_sent()){
            foreach($this->headers as $header=>$value){
                header($header.':'.$value, true);
            }
            foreach($this->cookies as $cookie){
                setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpires(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
            }
            if($this->redirect){
                header('Location: ' . $this->redirect);
                exit;
            }
        }
        
        echo $this->output;
        exit;
    }
}