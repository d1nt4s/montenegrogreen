<?php

namespace Dantes\Montegreen\Statuses\Estate\Input;

class Type implements InputInterface
{

    function __construct(private $telegram, private $db, private $keyboards) {}
    public function ask($parameters)
    {
        $instruction_message = "Выберите тип обьекта.";

        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=>  $instruction_message,
            'reply_markup' => new \Telegram\Bot\Keyboard\Keyboard($this->keyboards['entering_object_type']),
        ]);

        // УКАЗЫВАЕМ В БАЗЕ ДАННЫХ ОБЯЗАТЕЛЬНЫЙ ПАРАМЕТР ДЛЯ ПРАВИЛЬНОГО ПЕРЕХОДА ДАЛЕЕ
        // $this->db->query("UPDATE users SET last_message=? WHERE id=?", ['stage_type', $parameters['chat_id']]); 
    }

    public function put($parameters, $object_id)
    {
        $this->db->query("UPDATE estate_objects SET type=? WHERE id=?", [$parameters['message_text'], $object_id]);
    }

}