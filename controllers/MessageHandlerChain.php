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

    function getChatID($update)
    {
        if (isset($update['message']['chat']['id'])) {
            return $update['message']['chat']['id'];
        } elseif (isset($update['callback_query']['message']['chat']['id'])) {
            return $update['callback_query']['message']['chat']['id'];
        } else
            return null;
    }

    function process_message($update)
    {
        $text = $update['message']['text'] ?? '';
        $chat_id = $this->getChatID($update);

        foreach ($this->handlers as $handler) {
            if ($handler->can_handle($text)) {
                $handler->handle($chat_id);
                break;
            }
        }
    }



}