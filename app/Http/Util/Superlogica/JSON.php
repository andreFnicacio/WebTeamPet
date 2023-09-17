<?php
/**
 * Created by PhpStorm.
 * User: ios
 * Date: 04/11/16
 * Time: 10:45
 */

namespace App\Http\Util\Superlogica;


class JSON {
    public static function encode($source, $flag = null) {
        if($flag) {
            return json_encode($source, $flag);
        }
        
        return json_encode(self::utf8_encode_deep($source));
    }

    /**
     * Realiza o encoding UTF-8 recursivo para todas as 
     * propriedades caso a string não seja previamente UTF-8
     */
    public static function utf8_encode_deep(&$input) {
        if (is_string($input)) {
            $decoded = utf8_decode($input);
            if (mb_detect_encoding($decoded , 'UTF-8', true)) {
                $input = utf8_encode($input);
            }
        } else if (is_array($input)) {
            foreach ($input as &$value) {
                self::utf8_encode_deep($value);
            }
            
            unset($value);
        } else if (is_object($input)) {
            $vars = array_keys(get_object_vars($input));
            
            foreach ($vars as $var) {
                self::utf8_encode_deep($input->$var);
            }
        }
        return $input;
    }

    public static function decode($string) {
        return json_decode($string);
    }

    public static function response($source) {
        header('Content-Type: application/json');
        echo self::encode($source);
    }
}