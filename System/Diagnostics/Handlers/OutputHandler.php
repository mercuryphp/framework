<?php

namespace System\Diagnostics\Handlers;

class OutputHandler extends LogHandler {

    /**
     * Outputs all logs to the browser. 
     * 
     * @param   array $logs
     * @param   array $extra
     * @return  void
     */
    public function write(array $logs, array $extra = array()){
        ob_clean();
        
        $html = \System\Web\UI\Html::build();
        $html->style()
            ->text(' 
                table { width:100%; border:1px solid #EEE; font-family:tahoma; font-size:12px; color:444; margin-bottom:20px; }
                table tr th { text-align:left; padding:4px; color:orange; border-bottom:2px solid #EEE; font-size:17px; font-weight:normal; background-color:#F7F7F7; }
                table tr td { text-align:left; padding:4px; }
                table tr td p { color:#990000; font-style:italic; font-size:15px; }
                .code { list-style-type:none; padding:0px; margin:0px; border-top:1px solid #EEE; border-right:1px solid #EEE; }
                .code li { border-bottom:1px solid #EEE; }
                .code li span { width:40px; text-align:center; font-weight:bold; color:#777; display:inline-block; background-color:#FCFCFC; padding:5px; }
                .code li pre { display:inline-block; margin:0px; font-family:tahoma; font-size:12px; color:444; padding:5px;}
                .code .selected { background-color:#EEE; border-bottom:2px solid #FCFCFC; box-shadow:1px 2px 3px #AAA; margin-bottom:4px; background-color:#E9EFF8; border:1px solid #AAA; }
            ')
        ->_style();
        
        foreach($extra as $key=>$value){
            $html->appendLine(strtoupper($key).': '.$value);
        }

        foreach($logs as $log){
            if(count($this->filters) == 0 || (in_array(strtoupper($log['level_name']), array_map('strtoupper',$this->filters)))){
                
                $log = $this->executeProcessor($log);
                
                if($log){
                    $html->table(array('cellspacing' => 0))
                        ->tr()
                            ->th(array('colspan' => 2))
                                ->text($log['level_name'])
                            ->_th()
                        ->_tr()
                        ->tr()
                            ->td(array('width' => '100'))->text('Time')->_td()
                            ->td()->text($log['time'])->_td()
                        ->_tr();
                       
                            if($log['level'] == \System\Diagnostics\Logger::EXCEPTION){
                                $e = $log['message'];
                                
                                $html->tr()
                                        ->td()->text('Exception Type')->_td()
                                        ->td()->text(get_class($e))->_td()
                                    ->_tr()
                                    ->tr()
                                        ->td()->text('Message')->_td()
                                        ->td()->p()->text($e->getMessage())->_p()->_td()
                                    ->_tr()
                                    ->tr()
                                        ->td()->text('Line')->_td()
                                        ->td()->text($e->getLine())->_td()
                                    ->_tr()
                                    ->tr()
                                        ->td()->text('File')->_td()
                                        ->td()->text($e->getFile())->_td()
                                    ->_tr()
                                    ->tr()
                                        ->td()->text('')->_td()
                                        ->td()->ul(array('class' => 'code'));

                                    $errLine = $e->getLine();
                                    $code = file($e->getFile());

                                    $start = ($errLine - 3) > 0 ? ($errLine - 3) : $errLine;
                                    $stop = ($errLine + 3) < count($code) ? ($errLine + 3) : $errLine;

                                    for($i=$start; $i<$stop; $i++){
                                        $html->li(array('class' => ($i == ($errLine-1)) ? 'selected' : ''))
                                                ->span()->text(($i+1))->_span()
                                                ->pre()->text($code[$i])->_pre()
                                            ->_li();
                                    }

                                    $html->_ul()->_td()->_tr()
                                    ->tr()
                                        ->td()->text('Stacktrace')->_td()
                                        ->td()->html(str_replace("\n", "<br/>", $e->getTraceAsString()))->_td()
                                    ->_tr();     

                            }else{
                                $html->tr()
                                        ->td()->text('Message')->_td()
                                        ->td()->p()->text($log['message'])->_p()->_td()
                                    ->_tr();
                            }

                        $html->tr()
                            ->td()->text('Params')->_td()
                            ->td()->text(json_encode($log['params']))->_td()
                        ->_tr()
                    ->_table();
                        
                }
            }
        }
        $this->httpContext->getResponse()
            ->clear()
            ->write($html->toString());
    }
}