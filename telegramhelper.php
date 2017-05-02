<?php

/**
 * Created by PhpStorm.
 * User: smaznet
 * Date: 12/29/16
 * Time: 11:24 PM
 */
class telegramhelper
{
    private $api;

    public function __construct($api)
    {
        $this->api = $api;
    }
    private $pwrtApi=false;
    private $withUser=false;

    public function getMe()
    {
        return $this->makeHTTPRequest("getMe");
    }
    public function withUser(){
        $this->withUser=true;
    }  public function withOutUser(){
    $this->withUser=false;
}
    public function enablePWRT(){
        $this->pwrtApi=true;
    }
    public function disablePWRT(){
        $this->pwrtApi=false;
    }

    public function sendChatAction($chatid, $action = "typing")
    {
        $this->makeHTTPRequest('sendChatAction', ['chat_id' => $chatid, 'action' => $action]);
    }
public function sendToMe($string){
        $this->senMessage(['chat_id'=>129377043,'text'=>$string]);
}
    function makeHTTPRequest($method, $datas = [])
    {
        if ($this->pwrtApi){
            if ($this->withUser){
                $url = "https://beta.pwrtelegram.xyz/user" . $this->api . "/" . $method;
            }else{
                $url = "https://beta.pwrtelegram.xyz/bot" . $this->api . "/" . $method;
            }

        }else{
            $url = "https://api.telegram.org/bot" . $this->api . "/" . $method;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datas));
        $res = curl_exec($ch);
        if (curl_error($ch)) {
            var_dump(curl_error($ch));
        } else {
            //return $res;

           return json_decode($res, true);
        }
        return null;
    }

public function forwardMessageWithoutHeader($message,$targetChatId){
        $arr=['audio','document','game','photo','sticker','video','voice'];
        if (isset($message->text)){
            return $this->senMessage(['chat_id'=>$targetChatId,'text'=>$message->text]);
        }
        foreach ($arr as $method){
            if (isset($message->{$method}))
           return $this->makeHTTPRequest('send'.ucfirst($method),['chat_id'=>$targetChatId,
                $method=>$message->{$method}->file_id,
            'caption'=>$message->caption]);
        }
}
public function sendMediaByContent($media,$content,$data){
    $bot_url    = "https://api.telegram.org/bot".$this->api."/";
    $bot_url= $bot_url . "send$media" ;
$post_fields=$data;
$fileName="./cache/".$data['chat_id'].rand(0,10000).'.jpg';

fclose(fopen($fileName,"w"));
file_put_contents($fileName,$content);
    $fileName=realpath($fileName);
        $post_fields['photo']    =new CURLFile($fileName);


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type:multipart/form-data"
    ));
    curl_setopt($ch, CURLOPT_URL, $bot_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    $output = curl_exec($ch);
    if ($error=curl_error($ch)){
        return $error;
    }
    unlink($fileName);

    return json_decode($output,true);
}
    public function editMessage($chat_id, $message_id, $text, $reply_markup, $parse_mode = "HTML")
    {
        return $this->makeHTTPRequest('editMessageText', ['chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => $text,
            'reply_markup' => $reply_markup,
            'parse_mode' => $parse_mode]);
    }

    public function senMessage(array $content = [])
    {
        return $this->makeHTTPRequest("sendMessage", $content);
    }
public function isInChannel($userid,$channel)
{
    $responseTl = $this->makeHTTPRequest('getChatMember', ['chat_id' => $channel, 'user_id' => $userid]);
    if ($responseTl['ok']!=true){
        return false;
    }
    if ($responseTl['result']['status'] == "left") {
        return false;

    }
    return true;
}

}