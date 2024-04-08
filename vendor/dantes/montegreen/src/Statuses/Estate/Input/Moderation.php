<?php

namespace Dantes\Montegreen\Statuses\Estate\Input;

class Moderation
{
    private $moderator_chat_id = 387634516;

    function __construct(private $telegram, private $db, private $keyboards) {}

    public function informModerator($object_id)
    {
        $this->telegram->sendMessage([
            'chat_id' => $this->moderator_chat_id,
            'text'=> "Модератор! К вам на проверку поступил новый обьект! Его ID = {$object_id}",
            'reply_markup' => new \Telegram\Bot\Keyboard\Keyboard($this->keyboards['show_new_object']),
        ]);
    }

}