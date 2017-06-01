<?php

/**
 * Created by PhpStorm.
 * User: smaznet
 * Date: 12/29/16
 * Time: 11:24 PM.
 */
class telegramhelper
{
    private $api;

    public function __construct($api)
    {
        $this->api = $api;
    }

    private $pwrtApi = false;
    private $withUser = false;

    public function getMe()
    {
        return $this->makeHTTPRequest('getMe');
    }

    public function withUser()
    {
        $this->withUser = true;
    }

    public function withOutUser()
    {
        $this->withUser = false;
    }

    public function enablePWRT()
    {
        $this->pwrtApi = true;
    }

    public function disablePWRT()
    {
        $this->pwrtApi = false;
    }

    public function fileUrl($path)
    {
        return 'https://api.telegram.org/file/bot'.$this->api.'/'.$path;
    }

    public function sendChatAction($chatid, $action = 'typing')
    {
        $this->makeHTTPRequest('sendChatAction', ['chat_id' => $chatid, 'action' => $action]);
    }

    public function buildMultiPartRequest($ch, $boundary, $fields, $files, $fileNames = [])
    {
        $delimiter = ''.$boundary;
        $data = '';
        foreach ($fields as $name => $content) {
            $data .= '--'.$delimiter."\r\n"
                .'Content-Disposition: form-data; name="'.$name."\"\r\n\r\n"
                .$content."\r\n";
        }
        foreach ($files as $name => $content) {
            if (!isset($fileNames[$name])) {
                $fileNames[$name] = $name;
            }
            $data .= '--'.$delimiter."\r\n"
                .'Content-Disposition: form-data; name="'.$name.'"; filename="'.$fileNames[$name].'"'."\r\n\r\n"
                .$content."\r\n";
        }
        $data .= '--'.$delimiter."--\r\n";
        curl_setopt_array($ch, [
            CURLOPT_POST       => true,
            CURLOPT_HTTPHEADER => [
                'Content-Disposition: form-data; name="image"; filename="file.jpeg',
                "Content-Type: multipart/form-data; boundary=$boundary",
            ],
            CURLOPT_POSTFIELDS => $data,
        ]);

        return $ch;
    }

    public function sendToMe($string)
    {
        $this->senMessage(['chat_id'=>129377043, 'text'=>$string]);
    }

    public function makeHTTPRequest($method, $datas = [])
    {
        if ($this->pwrtApi) {
            if ($this->withUser) {
                $url = 'https://beta.pwrtelegram.xyz/user'.$this->api.'/'.$method;
            } else {
                $url = 'https://beta.pwrtelegram.xyz/bot'.$this->api.'/'.$method;
            }
        } else {
            $url = 'https://api.telegram.org/bot'.$this->api.'/'.$method;
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
    }

    public function forwardMessageWithoutHeader($message, $targetChatId, $reply_markUp = null)
    {
        $arr = ['audio', 'document', 'game', 'photo', 'sticker', 'video', 'voice'];

        if (isset($message->text)) {
            $sendData = ['chat_id'=>$targetChatId, 'text'=>$message->text];
            if (!empty($reply_markUp)) {
                $sendData['reply_markup'] = $reply_markUp;
            }

            return $this->senMessage($sendData);
        }
        foreach ($arr as $method) {
            if ($method == 'photo') {
                $message->photo = $message->photo[count($message->photo) - 1];
            }
            $sendData = ['chat_id'=> $targetChatId,
                $method           => $message->{$method}->file_id,
                'caption'         => $message->caption, ];
            if (!empty($reply_markUp)) {
                $sendData['reply_markup'] = $reply_markUp;
            }
            if (isset($message->{$method})) {
                return $this->makeHTTPRequest('send'.ucfirst($method), $sendData);
            }
        }
    }

    public function sendMediaByContent($media, $content, $data, $fileName = null, $progress = false)
    {
        $bot_url = 'https://api.telegram.org/bot'.$this->api.'/';
        $bot_url = $bot_url."send$media";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type:multipart/form-data',
    ]);
        curl_setopt($ch, CURLOPT_URL, $bot_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($progress) {
            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progress');
            curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        }
        $headers[] = 'Expect: 100-continue';
        $boundry = uniqid();
        $headers[] = "Content-Type: multipart/form-data; boundary=$boundry";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($fileName == null) {
            $ch = $this->buildMultiPartRequest($ch, $boundry, $data, [lcfirst($media) => $content]);
        } else {
            $ch = $this->buildMultiPartRequest($ch, $boundry, $data, [lcfirst($media) => $content], [lcfirst($media) => $fileName]);
        }
        $output = curl_exec($ch);
        if ($error = curl_error($ch)) {
            return $error;
        }

        return json_decode($output, true);
    }

    public function editMessage($chat_id, $message_id, $text, $reply_markup, $parse_mode = 'HTML')
    {
        return $this->makeHTTPRequest('editMessageText', ['chat_id' => $chat_id,
            'message_id'                                            => $message_id,
            'text'                                                  => $text,
            'reply_markup'                                          => $reply_markup,
            'parse_mode'                                            => $parse_mode, ]);
    }

    public function senMessage(array $content = [])
    {
        return $this->makeHTTPRequest('sendMessage', $content);
    }

    public function isInChannel($userid, $channel)
    {
        $responseTl = $this->makeHTTPRequest('getChatMember', ['chat_id' => $channel, 'user_id' => $userid]);
        if ($responseTl['ok'] != true) {
            return false;
        }
        if ($responseTl['result']['status'] == 'left') {
            return false;
        }

        return true;
    }

    public function answerCallbackQuery($cid, $text = null, $notfi = false)
    {
        return $this->makeHTTPRequest('answerCallbackQuery', ['callback_query_id'=>$cid, 'text'=>$text, 'show_alert'=>$notfi]);
    }

    public function api(BaseMethod $baseMethod)
    {
        return $this->makeHTTPRequest(get_class($baseMethod), $baseMethod->buildParams());
    }
}
