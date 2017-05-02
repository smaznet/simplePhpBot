<?php

/**
 * Created by PhpStorm.
 * User: smaznet
 * Date: 1/10/17
 * Time: 2:40 PM
 */
class Utils
{
 

    public static function contain($str, $needable)
    {
        return (strpos($str, $needable) !== false);
    }

    public static function toPersianNums($engnumbersstr)
    {

        $persian = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $num = range(0, 9);
        return str_replace($num, $persian, $engnumbersstr);
    }

  

    public static function toEngNums($persiannums)
    {

        $persian = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $num = range(0, 9);

        return str_replace($persian, $num, $persiannums);
    }
}
