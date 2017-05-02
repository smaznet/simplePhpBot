<?php

/**
 * Created by PhpStorm.
 * User: smaznet
 * Date: 1/9/17
 * Time: 7:25 PM
 */
class KeyBoardMarkUp
{
    public static function build(bool $needBackBtn,array $keyboard,$resize_keyboard=true,$one_time_keyboard=false,$selective=false)
    {
        if ($needBackBtn){
            $keyboard=array_merge($keyboard,[[KeyBoardItem::build("بازگشت به منوی اصلی","back",0)]]);
        }
return ['keyboard'=>$keyboard,
'resize_keyboard'=>$resize_keyboard,
"one_time_keyboard"=>$one_time_keyboard,
"selective"=>$selective];
    }

}