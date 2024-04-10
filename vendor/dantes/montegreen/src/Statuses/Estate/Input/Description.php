<?php

namespace Dantes\Montegreen\Statuses\Estate\Input;

class Description implements InputInterface
{

    function __construct(private $telegram, private $db) {}
    public function ask($parameters)
    {
        $instruction_message = "Напишите описание обьекта";

        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=>  $instruction_message,
        ]);
    }

    public function put($parameters, $object_id)
    {
        $data = htmlentities(trim($parameters['message_text']));
        if (mb_strlen($data, 'UTF-8') >= 600 || mb_strlen($data, 'UTF-8') <= 0) {
            $this->telegram->sendMessage([
                'chat_id' => $parameters['chat_id'],
                'text'=>  "Описание превышает норму в 600 слов!",
            ]);
            return false;
        }
        $this->db->query("UPDATE estate_objects SET description=? WHERE id=?", [$data, $object_id]);
        return true;
    }

}