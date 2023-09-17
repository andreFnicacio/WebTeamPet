<?php
/**
 * Created by PhpStorm.
 * User: ios
 * Date: 29/12/16
 * Time: 11:34
 */

namespace App\Http\Util\Superlogica;


class Session {

    public static function start() {
        if (!self::started()) {
            session_start();
        }
    }

    public static function started() {
        return session_status() !== PHP_SESSION_NONE;
    }

    public static function destroy() {
        return session_destroy();
    }

    /**
     * 
     * @param type $key Chave identificadora no array
     * @param type $value Valor destinado para gravação
     * @param type $serialize Aplicar método de serialização antes de gravar
     */
    public static function write($key, $value, $serialize = false) {
        self::start();
        if($serialize) {
            $value = serialize($value);
        }
        $_SESSION[$key] = $value;
    }

    public static function read($key, $unserialize = false) {
        self::start();

        if(isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
            if($unserialize) {
                return unserialize($value);
            }
            return $value;
        }

        return null;
    }
}