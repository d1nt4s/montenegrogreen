<?php

namespace Dantes\Montegreen\Statuses\Estate\Input;

class Link implements InputInterface
{
    private $instruction_message = <<<EOD
    Если имеется, укажите ссылку на свой обьект. То может быть ссылка на крупный веб-портал или ваш сайт.
    Обратите внимание, что ссылка должна начинаться на http:// или https://, в противном случае ссылка не пройдет.
    Если ссылка отсутствует, нажмите кнопку 'Пропустить'.
    EOD;
    function __construct(private $telegram, private $db, private $keyboards) {}
    public function ask($parameters)
    {
        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=>  $this->instruction_message,
            'reply_markup' => new \Telegram\Bot\Keyboard\Keyboard($this->keyboards['skip']),
        ]);
    }

    public function put($parameters, $object_id)
    {
        // Если была нажата кнопка 'Пропустить' в функции ask
        if($parameters['message_text'] === 'skip_facility') {
            return true;
        }

        // Валидация данных
        $data = htmlentities(trim($parameters['message_text']));
        if (mb_strlen($data, 'UTF-8') >= 256 || mb_strlen($data, 'UTF-8') <= 0 || !get_headers($data, 1)) {
            $this->telegram->sendMessage([
                'chat_id' => $parameters['chat_id'],
                'text'=>  "Ссылка превышает 256 символов или не является ссылкой!",
            ]);
            return false;
        }

        // Запись данных
        $this->db->query("UPDATE estate_objects SET link=? WHERE id=?", [$data, $object_id]);
        return true;
    }

}