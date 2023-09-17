<?php
namespace App\Helpers;
use Carbon\Carbon;

class Filtro {
    public static function setSelected($find, $params, $key) {
        $selected = "selected=selected";
        if(!isset($params[$key])) {
            return null;
        }
        if(is_array($params[$key])) {
            if(in_array($find, $params[$key])) {
                return $selected;
            }
        } else {


            if($find == $params[$key]) {
                return $selected;
            }
        }

        return null;
    }

    public static function getDateTime($date, $start = true) {

        if(isset($date)) {
            $datetime = Carbon::createFromFormat('d/m/Y', $date);
        } else {
            $datetime = Carbon::createFromTimestamp(0);
        }
        

        if($start == true) {
            $datetime->setTime(0,0,0);
        } else {
            $datetime->setTime(23,59,59);
        }
        
        return $datetime;
    }

    public static function getDecimal($string) {
        return str_replace(',', '.', str_replace('.', '', $string));
    }
}