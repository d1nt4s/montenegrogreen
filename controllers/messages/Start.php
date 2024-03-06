<?php

class Start
{
    public $telegram;
    public $keyboards;

    private $hello = <<<EOD
    Найди себе недвижимость с ботом Montenegro Green!
     - Для того, чтобы посмотреть все обьекты выбери
    вкладку "Посмотреть все обьекты"
     - Чтобы выбрать определенный обьект зайди во вкладку
     "Обьекты" и выбери нужный
     - Чтобы подобрать себе обьект по определенным хар-ам
     набери /filtrate и выбери характеристики
    EOD;

    function __construct($telegram, $keyboards)
    {
        $this->telegram = $telegram;
        $this->keyboards = $keyboards;
    }

    function can_handle($text)
    {
        if ($text == '/start' || $text == 'Как пользоваться ботом') {
            return true;
        } else {
            return false;
        }
    }

    function handle($chat_id)
    {
        $this->telegram->sendMessage([
            'chat_id' => $chat_id,
            'text'=> $this->hello,
            'reply_markup' => json_encode($this->keyboards['base_keyboard']),
        ]);
    }

}