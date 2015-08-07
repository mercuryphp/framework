<?php

namespace System\Web\Mvc\Edge;

class Compiler {
    
    protected $lexer = null;
    
    public function __construct(Lexer $lexer){
        $this->lexer = $lexer;
    }
    
    public function compile(){
        $tokens = $this->lexer->getTokens();
        $code = '<?php ';
        $echo = true;
        $nextToken = null;
        
        foreach($tokens as $idx => $token){
            $nextToken = isset($tokens[$idx+1]) ? $tokens[$idx + 1] : null;
            if(isset($token['type'])){
                switch($token['type']){
                    case 'T_OPEN_BRACE':
                        $code .= '{';
                        break;
                    case 'T_CLOSE_BRACE':
                        $code .= '}';
                        break;
                    case 'T_SEMI_COLON':
                        $code .= ':';
                        break;
                    case 'T_EPR':
                        $token = $token['token'];
                        if($token){
                            $code .= '(' . $token . ')';
                        }
                        break;
                    case 'T_HTML':
                        if($echo){
                            $output = $token['token'];
                            if($output){
                                $code .= 'echo "' . str_replace('"', '\"', $output) . '";';
                            }
                        }
                        break;
                    case 'T_ELSE':
                        $code .= '}else';
                        break;
                    case 'T_ELSEIF':
                        $code .= '}elseif';
                        break;
                    case 'T_SWITCH':
                        $code .= 'switch';
                        $echo = false;
                        break;
                    case 'T_CASE':
                        $code .= 'case';
                        $echo = true;
                        break;
                    case 'T_BREAK':
                        $code .= 'break;';
                        break;
                    case 'T_ESCAPE_ECHO':
                        $code .= 'echo $this->escape('.$token['token'].');';
                        break;
                    case 'T_PHP_CODE':
                        $code .= '?><?'.$token['token'].'?><?php ';
                        break;
                    case 'T_FUNCTION':
                        if($nextToken){
                            if($nextToken['type'] != 'T_FUNCTION_ARGS'){
                                throw new \Exception('Parse error on line ' .$token['line'] . ' near @' . $token['token'] . '. Missing semi-colon.');
                            }
                        }
                        $code .= '$this->'.$token['token'];
                        break;
                    case 'T_FUNCTION_ARGS':
                        $code .= $token['token'].';';
                        break;
                    default:
                        $code .= substr($token['token'], 1);
                }
            }
            $previousToken = $token;
        }
        return $code;
    }
}