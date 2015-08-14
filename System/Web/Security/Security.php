<?php

namespace System\Web\Security;

final class Security {

    public static function hmac($type, $string, $key){
        return hash_hmac($type, $string, $key);
    }
    
    public static function iv($cipher, $mode, $source, $base64 = true){
        if($base64){
            return base64_encode(mcrypt_create_iv(mcrypt_get_iv_size($cipher, $mode), $source));
        }
        return mcrypt_create_iv(mcrypt_get_iv_size($cipher, $mode), $source);
    }
    
    public static function encrypt($cipher, $key, $data, $mode, $iv, $base64 = true){
        if($base64){
            return base64_encode(mcrypt_encrypt($cipher, $key, $data, $mode, $iv));
        }
        return mcrypt_encrypt($cipher, $key, $data, $mode, $iv);
    }
    
    public static function decrypt($cipher, $key, $data, $mode, $iv, $base64 = true){
        if($base64){
            $data = base64_decode($data);
        }
        return mcrypt_decrypt($cipher, $key, $data, $mode, $iv);
    }
}