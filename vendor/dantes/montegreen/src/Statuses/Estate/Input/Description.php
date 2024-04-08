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
        $this->db->query("UPDATE estate_objects SET description=? WHERE id=?", [$parameters['message_text'], $object_id]);
    }

}