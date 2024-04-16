<?php

namespace Dantes\Montegreen\Statuses\Estate\Input;

class Rooms implements InputInterface
{

    function __construct(private $telegram, private $db, private $keyboards) {}
    public function ask($parameters)
    {
        $instruction_message = "Введите количество комнат";

        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=>  $instruction_message,
            'reply_markup' => new \Telegram\Bot\Keyboard\Keyboard($this->keyboards['skip']),
        ]);
    }

    public function put($parameters, $object_id)
    {
        $data = htmlentities(trim($parameters['message_text']));
        if ($data == 'skip_facility') {
            $object_type = $this->getObjectType($object_id);
            if (strcmp($object_type, "type_hotel") == 0 || strcmp($object_type, "type_land") == 0 || strcmp($object_type, "type_commercy") == 0) {
                return true;
            } else {
                $this->telegram->sendMessage([
                    'chat_id' => $parameters['chat_id'],
                    'text'=>  "Пропустить ввод кол-ва комнат можно только если ваша недвижимость является отелем, землей или коммерческим обьектом!",
                ]);
                return false;
            }
        }
        if (!is_numeric($data)) {
            $this->telegram->sendMessage([
                'chat_id' => $parameters['chat_id'],
                'text'=>  "То, что вы ввели не является числом!",
            ]);
            return false;
        }
        if (intval($data) < 0 || intval($data) >= 255 || !is_int(intval($data))) {
            $this->telegram->sendMessage([
                'chat_id' => $parameters['chat_id'],
                'text'=>  "Ваше число отрицательное, слишком большое или не является целым!",
            ]);
            return false;
        }
        $this->db->query("UPDATE estate_objects SET rooms=? WHERE id=?", [$data, $object_id]);
        return true;
    }

    function getObjectType($id)
    {
        if ($this->db->query("SELECT * FROM estate_objects WHERE id=?", [$id])->find()) {
            return $this->db->query("SELECT * FROM estate_objects WHERE id=?", [$id])->find()['type'];
        } 
    }
}