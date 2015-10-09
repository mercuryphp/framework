<?php

namespace System\Diagnostics\Handlers;

class CookieHandler extends LogHandler {
    
    protected $httpResponse;
    protected $cookieName;
    
    public function __construct(\System\Web\HttpResponse $httpResponse, $cookieName){
        $this->httpResponse = $httpResponse;
        $this->cookieName = $cookieName;
    }
    
    public function write(array $logs, array $extra = array()){
        $logs = array_map(function($log){
            if($log['level_name'] == 'EXCEPTION'){
                $e = $log['message'];
                $log['message'] = $e->getMessage();
                $log['stack_trace'] = $e->getTraceAsString();
            }
           return $log;
    // 
    }, $logs); 

        $this->httpResponse
                ->getCookies($this->cookieName)
                ->setValue(json_encode(array('logs' => $logs, 'extra' => $extra)));
    }
}