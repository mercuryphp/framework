<?php

namespace System\Diagnostics\Handlers;

class JsonHandler extends LogHandler {

    /**
     * Outputs all logs to the browser in a JSON format. 
     * 
     * @param   array $logs
     * @param   array $extra
     * @return  void
     */
    public function write(array $logs, array $extra = array()){

        $response = array();
        
        foreach($extra as $key=>$value){
            $response[strtoupper($key)] = $value;
        }

        foreach($logs as $idx=>$log){
            if(count($this->filters) == 0 || (in_array(strtoupper($log['level_name']), array_map('strtoupper',$this->filters)))){
                
                $log = $this->executeProcessor($log);
                
                if($log){
                    if($log['level'] == \System\Diagnostics\Logger::EXCEPTION){
                        $ex = $logs[$idx]['message'];
                        $logs[$idx]['message'] = $ex->getMessage();
                        $logs[$idx]['file'] = $ex->getFile();
                        $logs[$idx]['line'] = $ex->getLine();
                        $logs[$idx]['trace'] = $ex->getTraceAsString();
                    }else{

                    }
                }
            }
        }
        $response['logs'] = $logs;
        
        $this->httpContext->getResponse()
            ->setContentType('application/json')
            ->setContentEncoding('UTF-8')
            ->clear()
            ->write(json_encode($response));
    }
}