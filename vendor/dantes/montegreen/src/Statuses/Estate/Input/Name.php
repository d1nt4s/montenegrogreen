<?php

namespace Dantes\Montegreen\Statuses\Estate\Input;

class Name implements InputInterface
{
    function __construct(private $telegram, private $db) {}
    public function ask($parameters)
    {
        $instruction_message = "Введите название обьекта видное всем пользователям для быстрого поиска вашего обьекта, а также для легкой идентификацией клиентом. Максимальное кол-во символов 31. Нецензурная лексика проверку не пройдет.";

        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=>  $instruction_message,
        ]);

    }

    public function put($parameters, $object_id)
    {
        $data = htmlentities(trim($parameters['message_text']));
        if (mb_strlen($data, 'UTF-8') >= 32 || mb_strlen($data, 'UTF-8') <= 0) {
            $this->telegram->sendMessage([
                'chat_id' => $parameters['chat_id'],
                'text'=>  "Название не соответствует требованиям! Оно должно быть от 1 до 31 символов длиной!",
            ]);
            return false;
        }
        $this->db->query("UPDATE estate_objects SET name=? WHERE id=?", [$data, $object_id]);
        return true;
    }

}