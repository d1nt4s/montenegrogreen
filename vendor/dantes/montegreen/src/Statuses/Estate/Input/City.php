<?php

namespace Dantes\Montegreen\Statuses\Estate\Input;

class City implements InputInterface
{

    function __construct(private $telegram, private $db, private $keyboards) {}
    public function ask($parameters)
    {
        $instruction_message = "Выберите город обьекта.";

        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=>  $instruction_message,
            'reply_markup' => new \Telegram\Bot\Keyboard\Keyboard($this->keyboards['entering_object_city']),
        ]);
    }

    public function put($parameters, $object_id)
    {
        if (!$parameters['isCallback']) {
            $this->telegram->sendMessage([
                'chat_id' => $parameters['chat_id'],
                'text'=>  "Выберите город из предоставленных.",
            ]);
            return false; 
        }
        $this->db->query("UPDATE estate_objects SET city=? WHERE id=?", [$parameters['message_text'], $object_id]);
        return true;
    }

}