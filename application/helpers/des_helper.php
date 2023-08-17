<?php

/*
  韋霖
*/
class DES
{
    public static function encode($data, $key)
    {
        $token = base64_encode(openssl_encrypt($data, 'des-ede3', $key));      
        $token = str_replace(array(' ', '/', '='), array('-', '_', ''), $token);
        return $token;  
    }

    public static function decode($data, $key)
    {
        $data = str_replace(array('-', '_'), array(' ', '/'), $data);
        return openssl_decrypt(base64_decode($data), 'des-ede3', $key);
    }
}