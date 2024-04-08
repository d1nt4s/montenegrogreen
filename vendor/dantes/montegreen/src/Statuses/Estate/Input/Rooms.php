<?php

namespace Dantes\Montegreen\Statuses\Estate\Input;

class Rooms implements InputInterface
{

    function __construct(private $telegram, private $db) {}
    public function ask($parameters)
    {
        $instruction_message = "Введите количество комнат";

        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=>  $instruction_message,
        ]);
    }

    public function put($parameters, $object_id)
    {
        $this->db->query("UPDATE estate_objects SET rooms=? WHERE id=?", [$parameters['message_text'], $object_id]);
    }

}