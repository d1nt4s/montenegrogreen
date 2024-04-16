<?php

namespace Dantes\Montegreen\Statuses\Estate\Input;

class City implements InputInterface
{
    private $instruction_message = <<<EOD
    Выберите город обьекта. Если ваш поселок/город отсутствуют в списке, то выберите наиближайший к нему и укажите точное местоположение в описании.
    EOD;
    function __construct(private $telegram, private $db, private $keyboards) {}
    public function ask($parameters)
    {
        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=>  $this->instruction_message,
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