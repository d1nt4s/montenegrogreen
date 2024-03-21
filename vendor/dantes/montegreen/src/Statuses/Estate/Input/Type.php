<?php

namespace Dantes\Montegreen\Statuses\Estate\Input;

class Type implements InputInterface
{
    public $keyboards;

    function __construct(private $telegram, private $db) 
    {
        require_once $GLOBALS['paths']['config'] . '/include.php';
        $this->keyboards = get_keyboard('input_estate');
    }
    public function ask($parameters)
    {
        $instruction_message = "Выберите тип обьекта и нажмите 'Продолжить'.";

        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=>  $instruction_message,
            // 'reply_markup' => new \Telegram\Bot\Keyboard\Keyboard($this->keyboards['choose_type']),
        ]);

        // УКАЗЫВАЕМ В БАЗЕ ДАННЫХ ОБЯЗАТЕЛЬНЫЙ ПАРАМЕТР ДЛЯ ПРАВИЛЬНОГО ПЕРЕХОДА ДАЛЕЕ
        // $this->db->query("UPDATE users SET last_message=? WHERE id=?", ['stage_type', $parameters['chat_id']]); 
    }

    public function put($parameters, $object_id)
    {
        // ОБРАБОТКА КЛАВИАТУРЫ В СООТВЕТСТВИИ С выбором пользователя
        $counter = 0;
        // foreach($this->keyboards['entering_object_type']['inline_keyboard'] as $choice) {
        //     if ($choice[0]['callback_data'] == $parameters['message_text']) {
        //         if (substr($this->keyboards['entering_object_type']['inline_keyboard'][$counter][0]['text'], -1) !== '✅')
        //             $this->keyboards['entering_object_type']['inline_keyboard'][$counter][0]['text'] .= '✅';
        //     }
        //     $counter++;
        // }

        $modified_object = $this->db->query("SELECT * FROM users WHERE id=?", [$parameters['chat_id']])->find()['modified_object'];
        $this->db->query("UPDATE estate_objects SET type=? WHERE id=?", [substr($parameters['message_text'],9), $modified_object]);
    }

}