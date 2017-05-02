# simplePhpBot
A Simple `PHP` bot Framework


# Example : 
``` php
$data=json_decode(file_get_contents("php://input"));
require ("Utils.php");
require ("telegramhelper.php");
$TL=new telegramhelper("363256212:YourBotTokenHere");

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
}
