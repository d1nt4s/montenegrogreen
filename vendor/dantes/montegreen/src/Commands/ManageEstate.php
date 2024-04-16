<?php

namespace Dantes\Montegreen\Commands;

use Exception;

class ManageEstate
{
    private $telegram;
    private $db;
    public $keyboards;
    private $how_to_manage = <<<EOD
    Это пункт управления вашими обьектами!
    Здесь вы можете посмотреть ваши обьекты, а также редактировать и удалять их.

    Для управления обьектом нажми слева от него /manage <<номер обьекта>>.
    Далее вам будет предоставлен выбор возможностей взаимодействия с вашим обьектом.
    EOD;

    function __construct($telegram, $db)
    {
        $this->telegram = $telegram;
        $this->db = $db;

        require_once $GLOBALS['paths']['config'] . '/include.php';
        $this->keyboards = get_keyboard('manage_estate');
    }

    function can_handle($parameters)
    {
        /* Показываем пользователю краткую информацию по его обьектам, а также кол-во его свободных мест. */
        if ($parameters['message_text'] == '/manage' || $parameters['message_text'] == 'Управлять своими обьектами') {
            $this->instruction($parameters);
        /* Меню управления отдельным обьектом */
        } elseif (str_contains($parameters['message_text'], '/manage_')) {
            if ($this->input_validation($parameters)) {
                $this->showEstateMenu($parameters);
            }
        /* Выбор варианта 'Редактировать' из меню обьекта */
        } elseif (str_contains($parameters['message_text'], 'MANAGE_ESTATE_EDIT')) {

        /* Выбор варианта 'Удалить' из меню обьекта */
        } elseif (str_contains($parameters['message_text'], 'MANAGE_ESTATE_DELETE')) {

        }
        
    }

    function handle($parameters) {}

    function instruction($parameters)
    {
        // ПРОВЕРИТЬ ЕСТЬ ЛИ ПОЛЬЗОВАТЕЛЬ В БАЗЕ, И ЕСЛИ НЕТ СОЗДАТЬ ЗАПИСЬ О НЕМ
        $this->createNewUser($parameters);

        $available_places = PHP_EOL . "Кол-во ваших свободных мест равно: {$this->getAvailablePlaces($parameters)}";

        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=> $this->how_to_manage . $available_places,
        ]);

        $estates = $this->showUserEstate($parameters);

        foreach($estates as $estate) {
            $this->telegram->sendMessage([
                'chat_id' => $parameters['chat_id'],
                'text'=> "/manage_{$estate['id']}" .' | '. $estate['name'] .' | '. $estate['type'] .' | '. $estate['city'] .' | '. $estate['price'],
            ]);
        }
    }

    function input_validation($parameters)
    {
        // Проверка, что полученная команда совпадает по размеру, что ее аргумент число, что этим обьектом владеет пользователь
        if (strlen($parameters['message_text']) > 16 || strlen($parameters['message_text']) < 9) {
            $this->telegram->sendMessage([
                'chat_id' => $parameters['chat_id'],
                'text'=> "Команда {$parameters['message_text']} не соответствует формату. Введите /manage_<<id_вашего_обьекта>>",
            ]); 
            return false;
        }
        $object_id = substr($parameters['message_text'], 8);
        if (!is_numeric($object_id)) {
            $this->telegram->sendMessage([
                'chat_id' => $parameters['chat_id'],
                'text'=> "Символы после /manage не являются числом! Введите /manage_<<id_вашего_обьекта>>",
            ]); 
            return false;
        } 
        if ($this->db->query("SELECT * FROM estate_objects WHERE id=?", [$object_id])->find()) {
            if (strcmp($this->db->query("SELECT * FROM estate_objects WHERE id=?", [$object_id])->find()['owner_id'], $parameters['chat_id']) === 0) {

                return true;

            } else {
                $this->telegram->sendMessage([
                    'chat_id' => $parameters['chat_id'],
                    'text'=> "Данный обьект принадлежит не вам!",
                ]); 
                return false;
            }

        } else {
            $this->telegram->sendMessage([
                'chat_id' => $parameters['chat_id'],
                'text'=> "Обьекта с id = {$object_id} не существует!",
            ]); 
            return false;
        }
    }

    function showEstateMenu($parameters)
    {
        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=> "Что вы хотите сделать с обьектом?",
            'reply_markup' => new \Telegram\Bot\Keyboard\Keyboard($this->keyboards['menu']),
        ]); 
    }

    function showUserEstate($parameters)
    {
        if ($this->db->query("SELECT * FROM estate_objects WHERE owner_id=?", [$parameters['chat_id']])->find()) {
            return $this->db->query("SELECT * FROM estate_objects WHERE owner_id=?", [$parameters['chat_id']])->findAll();
        } 
    }

    function createNewUser($parameters)
    {
        if (!$this->db->query("SELECT * FROM users WHERE id=?", [$parameters['chat_id']])->find()) {
            $this->db->query("INSERT INTO users (`id`, `available_places`, `last_message`) VALUES (?, ?, ?)", [$parameters['chat_id'], 3, $parameters['message_text']]);
        } 
    }

    function getAvailablePlaces($parameters)
    {
        if ($this->db->query("SELECT * FROM users WHERE id=?", [$parameters['chat_id']])->find()) {
            return $this->db->query("SELECT * FROM users WHERE id=?", [$parameters['chat_id']])->find()['available_places'];
        } 
    }

}