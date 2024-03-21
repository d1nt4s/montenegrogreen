<?php

namespace Dantes\Montegreen\Statuses\Estate\Input;

class Name implements InputInterface
{
    function __construct(private $telegram, private $db) {}
    public function ask($parameters)
    {
        $instruction_message = "Введите название обьекта, видное всем пользователям для быстрого поиска вашего обьекта. Максимальное кол-во символов 20.";

        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=>  $instruction_message,
        ]);

    }

    public function put($parameters, $object_id)
    {
        $this->db->query("UPDATE estate_objects SET name=? WHERE id=?", [$parameters['message_text'], $object_id]);
    }

}