<?php

namespace System\Web\Mvc\Edge;

class Lexer {
    
    protected $tokens = array();
    
    public function __construct($file = null){
        if($file){
            $this->tokenize($file);
        }
    }
    
    public function tokenize($file){
        if(is_file($file)){
            
            $lines = file($file);
            $lines[] = '@';
            $keywords = array(
                '@foreach' => 'T_LOOP', 
                '@for' => 'T_LOOP',
                '@if' => 'T_IF',
                '@else' => 'T_ELSE',
                '@elif' => 'T_ELSEIF',
                '@switch' => 'T_SWITCH',
                '@case' => 'T_CASE',
                '@break' => 'T_BREAK',
            );
            
            $functions = array(
                '@renderBody',
                '@setLayout',
                '@addScript',
                '@renderScripts',
            );
            


            $token = '';
            $codeBlock = 0;
            $captureEcho = false;
            $capturePhp = false;
            $function = false;

            foreach($lines as $l => $line){
                for($i=0; $i < strlen($line); $i++){
                    $chr = $line[$i];
                    $pchr = !isset($line[$i-1])?: $line[$i-1];
                    $nchr = !isset($line[$i+1])?: $line[$i+1];
                    
                    if(array_key_exists($token, $keywords)){
                        if(trim($chr) == ""){
                            $type = $keywords[$token];
                            $this->addToken($type, $token, $l);
                            $token = '';

                            if($type != 'T_ELSE'){
                                $codeBlock++;
                            }
                        }
                    }
                    
                    if(in_array($token, $functions) && $chr == '('){
                        $this->addToken('T_FUNCTION', substr($token, 1), $l);
                        $token = '';
                        $function = true;
                    }

                    if($chr =='@'){
                        $this->addToken('T_HTML', $token, $l);
                        $token = '';
                        
                        if($nchr =='('){
                            $captureEcho = true;
                            $i++;
                            continue;
                        }
                    }elseif($chr ==')' && $nchr ==';' && $captureEcho){
                        $captureEcho = false;
                        $i++;
                        $this->addToken('T_ESCAPE_ECHO', $token, $l);
                        $token = '';
                        continue;
                    }elseif($chr == '<' && substr($line, $i+1, 4) == '?php'){
                        $capturePhp = true;
                        $i++;
                        $this->addToken('T_HTML', $token, $l);
                        $token = '';
                        continue;
                    }elseif($chr == '?' & $nchr =='>' && $capturePhp){
                        $capturePhp = false;
                        $i++;
                        $this->addToken('T_PHP_CODE', $token, $l);
                        $token = '';
                        continue;
                    }elseif($chr ==')' && $nchr ==';' && $function){
                        $function = false;
                        $i++;
                        $this->addToken('T_FUNCTION_ARGS', $token.$chr, $l);
                        $token = '';
                        continue;
                    }
                    
                    if($codeBlock > 0){
                        if($pchr !='\\'){

                            if($chr == ':'){
                                $this->addToken('T_EPR', $token, $l);
                                $this->addToken('T_SEMI_COLON', ':', $l);
                                $token ='';
                                $openBrace = true;
                                continue;
                            }

                            if($chr == '{'){
                                $this->addToken('T_EPR', $token, $l);
                                $this->addToken('T_OPEN_BRACE', '{', $l);
                                $token ='';
                                $openBrace = true;
                                continue;
                            }

                            if($chr == '}'){
                                $this->addToken('T_HTML', $token, $l);
                                $this->addToken('T_CLOSE_BRACE', '}', $l);
                                $token ='';
                                $openBrace = false;
                                $codeBlock--;
                                continue;
                            }
                        }
                    }
                    $token .=$chr;
                }
            }
            $this->addToken('T_HTML', $token, $l);
        } //print_R($this->tokens); //exit;
    }
    
    public function getTokens(){
        return $this->tokens;
    }
    
    protected function addToken($type, $token, $line){
        $this->tokens[] = array('type' => $type, 'token' => $token, 'line' => $line + 1);
    }
}
