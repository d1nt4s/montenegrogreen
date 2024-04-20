<?php

namespace Dantes\Montegreen\Statuses\Estate\Input;

class Price implements InputInterface
{

    function __construct(private $telegram, private $db, private $keyboards) {}
    public function ask($parameters)
    {
        $instruction_message = "Введите цену (в евро).";

        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=>  $instruction_message,
        ]);
    }

    public function put($parameters, $object_id)
    {
        $data = htmlentities(trim($parameters['message_text']));
        if (!is_numeric($data)) {
            $this->telegram->sendMessage([
                'chat_id' => $parameters['chat_id'],
                'text'=>  "То, что вы ввели не является числом!",
            ]);
            return false;
        }
        if (intval($data) < 0 || intval($data) >= 16777215 || !is_int(intval($data))) {
            $this->telegram->sendMessage([
                'chat_id' => $parameters['chat_id'],
                'text'=>  "Ваше число отрицательное, слишком большое или не является целым!",
            ]);
            return false;
        }
        $this->db->query("UPDATE estate_objects SET price=? WHERE id=?", [$data, $object_id]);
        return true;
    }

}