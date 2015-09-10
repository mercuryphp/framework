<?php

namespace System\Web\Security;

class Authentication {
    
    private static $cookieName;
    private static $hashAlgor;
    private static $cipher;
    private static $encryptionKey;
    private static $validationKey;
    
    public static function setCookieName($cookieName){
        self::$cookieName = $cookieName;
    }
    
    public static function getCookieName(){
        return self::$cookieName;
    }
    
    public static function setHashAlgorithm($hashAlgor){
        self::$hashAlgor = $hashAlgor;
    }

    public static function setCipher($cipher){
        self::$cipher = $cipher;
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
        $hash = Security::hmac('sha256', $string, self::$validationKey);
        $iv = Security::iv(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC, MCRYPT_DEV_URANDOM, false);
        return Security::encrypt(self::$cipher, self::$encryptionKey, $hash.'|'.$string, MCRYPT_MODE_CBC, $iv, true).'|'.base64_encode($iv);
    }
    
    public static function decrypt($string){
        $sections = explode('|', $string);

        if(count($sections) == 2){
            $output = trim(Security::decrypt(MCRYPT_RIJNDAEL_256, self::$encryptionKey, $sections[0], MCRYPT_MODE_CBC, base64_decode($sections[1])));
            list($hash, $data) = explode('|', $output, 2);

            if($hash && $data){
                $hashed = Security::hmac('sha256', $data, self::$validationKey);

                if($hash == $hashed){
                    $data = unserialize($data);
                    return new AuthenticationTicket($data[0],$data[1],$data[2]);
                }
            }
        }
        return false;
    }
}