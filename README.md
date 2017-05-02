# simplePhpBot
A Simple `PHP` bot Framework


# Example : 
``` php
$data=json_decode(file_get_contents("php://input"));
require ("Utils.php");
require ("telegramhelper.php");
$TL=new telegramhelper("363256212:YourBotTokenHere");
/*
Something removed from example

*/
if(isset($data->message)){
    $chatId = $data->message->chat->id;
    $userid=$data->message->from->id;
    $message= $data->message;
    if(isset($message->text)){
          $text = $message->text;
          switch($text){
                case '/start':
 $Tl->senMessage(['chat_id' => $chatId, 'text' =>'Welceome , $data->message->from->first_name','parse_mode'=>'MARKDOWN','reply_markup'=>json_encode(InlineKeyBoardMarkUp::build(false,[
            [
                InlineKeyBoardItem::build('Lets Go inline query',null,null,'@')
            ],[
                InlineKeyBoardItem::build('Switch Language',null,'changeLang')
            ]
        ]))]);
          }
    }
}else if (isset($data->callback_query)){
    $query=$data->callback_query->data;
    $from=$data->callback_query->from->id;
    $strData=explode("_",$query);
    $chatId=$data->callback_query->message->chat->id;
    $messageId=$data->callback_query->message->message_id;

    $UM=new UserMan($from);

    if (empty($strData[1])||$strData[1]=='fa'){
        $UM->lang='en';
    }else{
        $UM->lang='fa';
    }
    $UM->save();
    $Tl->makeHTTPRequest('editMessageText',['chat_id' => $chatId,'message_id'=>$messageId, 'text' =>loadString('start',[$data->callback_query->from->first_name]),'parse_mode'=>'MARKDOWN','reply_markup'=>json_encode(InlineKeyBoardMarkUp::build(false,[
        [
            InlineKeyBoardItem::build(loadString('btnLetsGo'),null,null,'@')
        ],[
            InlineKeyBoardItem::build(loadString('btnSwitchLang'),null,'changeLang_'.$UM->lang)
        ]
    ]))]);
    $Tl->makeHTTPRequest('answerCallbackQuery',['callback_query_id'=>$data->callback_query->id,'text'=>loadString('langChanged')]);
}
