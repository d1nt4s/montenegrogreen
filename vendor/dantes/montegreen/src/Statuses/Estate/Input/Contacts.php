<?php

namespace Dantes\Montegreen\Statuses\Estate\Input;

class Contacts implements InputInterface
{

    function __construct(private $telegram, private $db, private $keyboards) {}
    public function ask($parameters)
    {
        $instruction_message = "Введите свои контакты, по которым клиент сможет вас найти. Рекомендуется номер телефона и telegram.";

        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=>  $instruction_message,
        ]);
    }

    public function put($parameters, $object_id)
    {
        $data = htmlentities(trim($parameters['message_text']));
        if (mb_strlen($data, 'UTF-8') >= 128 || mb_strlen($data, 'UTF-8') <= 0) {
            $this->telegram->sendMessage([
                'chat_id' => $parameters['chat_id'],
                'text'=>  "Текст не соответствует требованиям! Он должен быть от 1 до 127 символов длиной!",
            ]);
            return false;
        }
        $this->db->query("UPDATE estate_objects SET contacts=? WHERE id=?", [$data, $object_id]);
        return true;
    }

}