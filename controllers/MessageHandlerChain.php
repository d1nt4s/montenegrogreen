<?php

class MessageHandlerChain
{
    public $handlers;
    function __construct()
    {
        $this->handlers = array();
    }

    function add_handler($handler)
    {
        array_push($this->handlers, $handler);
    }

    function getTextChatID($update)
    {
        if (isset($update['callback_query'])) {
            return [
                'chat_id' => $update['callback_query']['message']['chat']['id'],
                'message_text' => $update['callback_query']['data'],
            ];
        } elseif (isset($update['message']['text'])) {
            return [
                'chat_id' => $update['message']['chat']['id'],
                'message_text' => $update['message']['text'],
            ];
        } else
            return null;
    }

    function process_message($update)
    {

        $text_chat_id = $this->getTextChatID($update);

        foreach ($this->handlers as $handler) {
            if ($handler->can_handle($text_chat_id['message_text'])) {
                $handler->handle($text_chat_id['chat_id']);
                break;
            }
        }
    }



}