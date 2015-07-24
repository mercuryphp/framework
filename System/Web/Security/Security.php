<?php

namespace System\Web\Security;

final class Security {
    
    const SHA256 = 'sha256';
    
    public static function hmac($type, $string, $key){
        return hash_hmac($type, $string, $key);
    }
    
    public static function iv($size, $source, $base64 = true){
        if($base64){
            return base64_encode(mcrypt_create_iv ($size, $source));
        }
        return mcrypt_create_iv ($size, $source);
    }
}