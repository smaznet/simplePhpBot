<?php
/**
 * Created by PhpStorm.
 * User: smaznet
 * Date: 1/9/17
 * Time: 7:19 PM
 */
class KeyBoardItem{
    public static function build($text,$code,$step)
    {
        return ["text"=>$text,"code"=>$code,"step"=>$step];
    }
}