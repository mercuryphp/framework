<?php

namespace System\Log\Handlers;

class SmtpHandler extends LogHandler {

    protected $settings;
    protected $conn;

    /**
     * Initializes an instance of SmtpHandler.
     * 
     * @method  __construct
     * @param   array $settings
     */
    public function __construct(array $settings){
        $this->settings = new \System\Collections\Dictionary($settings);
        $useSSL = (bool)$this->settings->get('useSSL');

        $host = ($useSSL) ? 'ssl://'.$this->settings->get('host') : $this->settings->get('host');
        $this->conn = fsockopen($host, $this->settings->get('port'), $errno, $errstr);
    }
    
    /**
     * Sends all logs via email. 
     * 
     * @method  write
     * @param   array $logs
     * @param   array $extra
     * @return  void
     */
    public function write(array $logs, array $extra = array()){
        if(is_resource($this->conn)){
            
            $message = (new \System\Std\String())->appendLine('');

            foreach($extra as $key=>$value){
                $message = $message->appendLine(strtoupper($key).': '.$value);
            }
            
            $send = false;
            foreach($logs as $log){ 
                if(count($this->filters) == 0 || (in_array(strtoupper($log['level_name']), array_map('strtoupper',$this->filters)))){

                    $log = $this->executeProcessor($log);
                    
                    if($log){
                        $message = $message->appendLine('LEVEL: '.$log['level_name'])
                            ->appendLine('DATE: '.$log['time'])
                            ->appendLine('PARAMS: '.  json_encode($log['params']))
                            ->appendLine('MESSAGE:')
                            ->appendLine($log['message'])
                            ->appendLine('');
                        $send = true;
                    }
                }
            }
            
            if(!$send){
                return;
            }

            $email  = "Date: " . date("D, j M Y G:i:s") . PHP_EOL;
            $email .= "From: " . $this->settings->get('from') . PHP_EOL;
            $email .= "Subject: " . $this->settings->get('subject') . PHP_EOL;
            $email .= "MIME-Version: 1.0" . PHP_EOL;
            $email .= "Content-Type:text/plain;";
            $email .=  PHP_EOL . (string)$message . PHP_EOL;
            $email .= "." . PHP_EOL;
                            
            $greeting = $this->getResponse();

            if($greeting->code ==220){ 
                if(strpos($greeting->message, 'ESMTP') > -1){
                    $this->sendCommand('EHLO '.$this->settings->get('host'));
                    $response = $this->getResponse(); 

                    if (strpos($response->message, 'STARTTLS') > -1){
                        $response = $this->startTLS();
                    }
                }else{
                    $this->sendCommand('HELO '.$this->settings->get('host'));
                    $response = $this->getResponse();
                }

                if($response->code == 250){
                    
                    if($this->settings->get('username') && $this->settings->get('password')){
                        
                        $this->sendCommand('AUTH LOGIN');
                        $response = $this->getResponse();
                
                        if($response->code == 334){
                            $this->sendCommand(base64_encode($this->settings->get('username')));
                            $response = $this->getResponse();

                            if($response->code == 334){
                                $this->sendCommand(base64_encode($this->settings->get('password')));
                                $response = $this->getResponse();
                                
                                if($response->code != 235){
                                    throw new SmtpException($response->message);
                                }
                            }
                        }
                    }

                    $this->sendCommand('MAIL FROM: <' . $this->settings->get('from') . '>');
                    $response = $this->getResponse();

                    if ($response->code == 250){
                        $recipients = $this->settings->get('rcpt');

                        foreach($recipients as $recipient){
                            $recipient = trim($recipient);
                            if($recipient){
                                $this->sendCommand('RCPT TO: <'. $recipient .'>');
                                $this->getResponse();
                            }
                        }

                        $this->sendCommand('DATA');
                        $response = $this->getResponse();

                        if ($response->code == 354){
                            fputs($this->conn, $email);
                            $response = $this->getResponse();

                            if ($response->code == 250){
                                return true;
                            }
                        }
                    }
                }
            }else{
                throw new \Exception($greeting->message);
            }
        }
    }
    
    private function sendCommand($command){
        if(is_resource($this->conn)){
            fputs($this->conn, $command . PHP_EOL);
        }
    }
    
    private function getResponse(){
        if(is_resource($this->conn)){
            $data="";
            while($str = fgets($this->conn)) {
                $data .= $str;
                if(substr($str,3,1) == " ") { break; }
            }

            $response = new \stdClass();
            $response->code = (int)\substr($data, 0, 3);
            $response->message = \substr($data, 3);
            return $response;
        }
    }
    
    private function startTLS(){
        $this->sendCommand('STARTTLS');
        $response = $this->getResponse();

        if($response->code == 220){
            stream_socket_enable_crypto($this->conn, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $this->sendCommand('EHLO '.$this->settings->get('host')); 
            return $this->getResponse();
        }else{
            throw new \Exception($response->messagage);
        }
    }
}