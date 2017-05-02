<?php
/**
 * Created by PhpStorm.
 * User: smaznet
 * Date: 1/9/17
 * Time: 7:19 PM
 */
class InlineKeyBoardItem{
    public static function build($text,$url=null,$callback_data=null,$switch_inline_query=null,$switch_inline_query_current_chat=null)
    {
        $response=["text"=>$text,"url"=>$url,"callback_data"=>$callback_data,"switch_inline_query"=>$switch_inline_query,'switch_inline_query_current_chat'=>$switch_inline_query_current_chat];
$response=array_filter($response);
        return $response;
    }
}