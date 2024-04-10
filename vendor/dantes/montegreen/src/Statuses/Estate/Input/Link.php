<?php

namespace Dantes\Montegreen\Statuses\Estate\Input;

class Link implements InputInterface
{

    function __construct(private $telegram, private $db) {}
    public function ask($parameters)
    {
        $instruction_message = "Если имеется, укажите ссылку на свой обьект. То может быть ссылка на крупный веб-портал или ваш сайт.";

        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=>  $instruction_message,
        ]);
    }

    public function put($parameters, $object_id)
    {
        $data = htmlentities(trim($parameters['message_text']));
        if (mb_strlen($data, 'UTF-8') >= 256 || mb_strlen($data, 'UTF-8') <= 0 || !get_headers($data, 1)) {
            $this->telegram->sendMessage([
                'chat_id' => $parameters['chat_id'],
                'text'=>  "Ссылка превышает 256 символов или не является ссылкой!",
            ]);
            return false;
        }
        $this->db->query("UPDATE estate_objects SET link=? WHERE id=?", [$data, $object_id]);
        return true;
    }

}