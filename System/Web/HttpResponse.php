<?php

namespace System\Web;

final class HttpResponse {

    private $headers;
    private $cookies;
    private $redirect;
    private $output;
    private $statusCode;
    private $contentType;
    private $encoding;
    private static $statusCodes = array(
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
    
    /**
     * Initializes a new instance of the HttpResponse class.
     * 
     * @method  __construct
     */
    public function __construct(){
        $this->cookies = new HttpCookieCollection(array());
        $this->headers = new \System\Collections\Dictionary();
        $this->setContentType('text/html');
        $this->setContentEncoding('UTF-8');
        $this->setStatusCode(200);
    }

    /**
     * Sets the HTTP content type of the output.
     * 
     * @method  setContentType
     * @param   string $contentType
     * @return  System.Web.HttpResponse
     */
    public function setContentType($contentType){
        $this->contentType = $contentType;
        $this->addHeader('Content-type', $this->contentType.';'.$this->encoding, true); 
        return $this;
    }
    
    /**
     * Gets the HTTP content type of the output.
     * 
     * @method  getContentType
     * @return  string
     */
    public function getContentType(){
        return $this->contentType;
    }
    
    /**
     * Sets the HTTP character set of the output.
     * 
     * @method  setContentEncoding
     * @param   string $encoding
     * @return  System.Web.HttpResponse
     */
    public function setContentEncoding($encoding){
        $this->encoding = $encoding;
        $this->addHeader('Content-type', $this->contentType.';charset='.$this->encoding, true); 
        return $this;
    }
    
    /**
     * Gets the HTTP character set of the output.
     * 
     * @method  getContentEncoding
     * @return  string
     */
    public function getContentEncoding(){
        return $this->encoding;
    }
    
    /**
     * Sets the content length of the output.
     * 
     * @method  setContentLength
     * @param   int $length
     * @return  System.Web.HttpResponse
     */
    public function setContentLength($length){
        $this->addHeader('Content-length', $length, true); 
        return $this;
    }
    
    /**
     * Gets the content length of the output.
     * 
     * @method  getContentLength
     * @return  int
     */
    public function getContentLength(){
        return (int)$this->headers->get('Content-length');
    }

    /**
     * Sets the HTTP status code of the output.
     * 
     * @method  setStatusCode
     * @param   int $code
     * @return  System.Web.HttpResponse
     */
    public function setStatusCode($code){
        if (isset(self::$statusCodes[$code])){
            $this->statusCode = (int)$code;
            $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
            header($protocol.' '.self::$statusCodes[$code]);
        }
        return $this;
    }
    
    /**
     * Gets the HTTP status code of the output.
     * 
     * @method  getStatusCode
     * @return  int
     */
    public function getStatusCode(){
        return $this->statusCode;
    }
    
    /**
     * Gets the HTTP header collection.
     * 
     * @method  getHeaders
     * @return  System.Collections.Dictionary
     */
    public function getHeaders(){
        return $this->headers;
    }
    
    /**
     * Gets a cookie from the cookie collection. Creates a new cookie if the cookie does not exist.
     * Gets the cookie collection if no name is specified.
     * 
     * @method  getCookies
     * @param   string $name
     * @return  mixed
     */
    public function getCookies($name = null){
        if($name){
            return $this->cookies->get($name);
        }
        return $this->cookies;
    }
    
    /**
     * Gets the HTTP response output.
     * 
     * @method  getOutput
     * @return  string
     */
    public function getOutput(){
        return $this->output;
    }

    /**
     * Adds a HTTP header to the header collection. Overrides an existing header 
     * if $override is set to true.
     * 
     * @method  addHeader
     * @param   string $header
     * @param   string $value
     * @param   bool $override = true
     * @return  System.Web.HttpResponse
     */
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
    
    /**
     * Writes a string to the response output.
     * 
     * @method  write
     * @param   string $output
     * @return  System.Web.HttpResponse
     */
    public function write($output){
        $this->output = $output;
        return $this;
    }
    
    /**
     * Appends a string to the response output.
     * 
     * @method  append
     * @param   string $output
     * @return  System.Web.HttpResponse
     */
    public function append($output){
        $this->output .= $output;
        return $this;
    }
    
    /**
     * Writes the string contents of the specified file to the response output.
     * 
     * @method  writeFile
     * @param   string $file
     * @return  System.Web.HttpResponse
     */
    public function writeFile($file){
        if(is_file($file)){
            $this->output = file_get_contents($file);
        }
        return $this;
    }
    
    /**
     * Redirects a client to a new URL. Immediately does a redirect if $immediateRedirect is set to true.
     * Setting $immediateRedirect to false will result in the script continuing execution until 
     * the application cycle is complete and the response objects flush() method has been called.
     * 
     * @method  redirect
     * @param   string $location
     * @param   bool $immediateRedirect
     * @return  void
     */
    public function redirect($location, $immediateRedirect = true){
        if(is_string($location)){
            if($immediateRedirect){
                header('Location: ' . $location);
                exit;
            }
            $this->redirect = $location;
        }
    }
    
    /**
     * Sends all output and headers to the client.
     * 
     * @method  flush
     * @return  string
     */
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
    }
}