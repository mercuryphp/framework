<?php

namespace System\Web\Security;

class FormsAuthentication {
    
    private static $cookieName;
    private static $encryptionKey;
    private static $validationKey;
    
    public static function setCookieName($cookieName){
        self::$cookieName = $cookieName;
    }
    
    public static function getCookieName(){
        return self::$cookieName;
    }

    public static function setEncryptionKey($encryptionKey){
        self::$encryptionKey = $encryptionKey;
    }
    
    public static function setValidationKey($validationKey){
        self::$validationKey = $validationKey;
    }
    
    public static function encrypt(AuthenticationTicket $ticket){
        $data = array(
            $ticket->getName(),
            $ticket->getExpire(),
            $ticket->getUserData(),
        );
        
        $string = serialize($data);
        $hash = hash_hmac('sha512', $string, self::$validationKey);
        
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, self::$encryptionKey, $hash.'|'.$string, MCRYPT_MODE_CBC, $iv)).'|'.base64_encode($iv);
    }
    
    public static function decrypt($string){
        $sections = explode('|', $string);
        
        if(count($sections) == 2){
            $message = base64_decode($sections[0]);
            $iv = base64_decode($sections[1]);

            $output = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, self::$encryptionKey, $message, MCRYPT_MODE_CBC, $iv));
            
            $sections = explode('|', $output, 2);

            if(count($sections) == 2){
                $hash = hash_hmac('sha512', $sections[1], self::$validationKey);
                if($hash == $sections[0]){
                    $data = unserialize($sections[1]);
                    return new AuthenticationTicket($data[0],$data[1],$data[2]);
                }
            }
        }
        return false;
    }
}
