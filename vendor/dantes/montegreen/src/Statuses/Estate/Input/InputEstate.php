<?php

namespace Dantes\Montegreen\Statuses\Estate\Input;

class InputEstate
{
    public $status_handler_name = "input";
    private $telegram;
    private $db;
    protected $estate_facilities = [];
    protected $moderation;
    function __construct($telegram, $db)
    {
        $this->telegram = $telegram;
        $this->db = $db;
        require_once $GLOBALS['paths']['config'] . '/include.php';
        $keyboards = get_keyboard('input_estate');

        $this->estate_facilities = [
            'name' => new Name($telegram, $db), 
            'type' => new Type($telegram, $db, $keyboards),
            'city' => new City($telegram, $db, $keyboards),
            'house_size' => new HouseSize($telegram, $db, $keyboards),
            'region_size' => new RegionSize($telegram, $db, $keyboards),
            'price' => new Price($telegram, $db),
            'rooms' => new Rooms($telegram, $db, $keyboards),
            'contacts' => new Contacts($telegram, $db),
            'link' => new Link($telegram, $db, $keyboards),
            'description' => new Description($telegram, $db),
        ];

        $this->moderation = new Moderation($telegram, $db, $keyboards);
    }

    public function process($parameters)
    {
        $GLOBALS['restart_bot'] = false;

        // Экстренный выход из статуса по требованию пользователя или напоминание ему об нахождении в состоянии статуса
        if ($parameters['message_text'] === '/stop') {
            $this->closeStatus($parameters['chat_id'], "Выход из статуса загрузки обьекта. Все функции бота снова доступны.", true);
        } else {
            $this->telegram->sendMessage([
                'chat_id' => $parameters['chat_id'],
                'text'=> 'Статус загрузки обьекта! Для прерывания введите /stop, место при этом не потратиться, однако введенные данные не запомняться! За помощью обращайтесь @d1ntes.',
            ]);
        }

        switch ($this->getStatusStage($parameters['chat_id']))
        {
            case ('create_ask_name'):
                $this->performStage($parameters, ['ask' => 'name', 'put' => ""], true, false, 'put_name_ask_type');
                break;
            case ('put_name_ask_type'):
                $this->performStage($parameters, ['ask' => 'type', 'put' => 'name'], false, false, 'put_type_ask_city');
                break;
            case ('put_type_ask_city'):
                $this->performStage($parameters, ['ask' => 'city', 'put' => 'type'], false, false, 'put_city_ask_housesize');
                break;
            case ('put_city_ask_housesize'):
                $this->performStage($parameters, ['ask' => 'house_size', 'put' => 'city'], false, false, 'put_housesize_ask_regionsize');
                break;
            case ('put_housesize_ask_regionsize'):
                $this->performStage($parameters, ['ask' => 'region_size', 'put' => 'house_size'], false, false, 'put_regionsize_ask_price');
                break;
            case ('put_regionsize_ask_price'):
                $this->performStage($parameters, ['ask' => 'price', 'put' => 'region_size'], false, false, 'put_price_ask_rooms');
                break;
            case ('put_price_ask_rooms'):
                $this->performStage($parameters, ['ask' => 'rooms', 'put' => 'price'], false, false, 'put_rooms_ask_contacts');
                break;
            case ('put_rooms_ask_contacts'):
                $this->performStage($parameters, ['ask' => 'contacts', 'put' => 'rooms'], false, false, 'put_contacts_ask_link');
                break;
            case ('put_contacts_ask_link'):
                $this->performStage($parameters, ['ask' => 'link', 'put' => 'contacts'], false, false, 'put_link_ask_description');
                break;
            case ('put_link_ask_description'):
                $this->performStage($parameters, ['ask' => 'description', 'put' => 'link'], false, false, 'put_description_finish');
                break;
            case ('put_description_finish'):
                $this->performStage($parameters, ['ask' => "", 'put' => 'description'], false, true, "");
                break;
        }

    }

    function performStage($parameters, $stage_members, $isFirstStage, $isLastStage, $nextStage)
    {
        if ($isFirstStage) {
            $this->createEstateObject($parameters);
            $this->estate_facilities[$stage_members['ask']]->ask($parameters);
        } else if ($isLastStage) {
            if (!$this->estate_facilities[$stage_members['put']]->put($parameters, $this->getObjectId($parameters['chat_id']))) { return; }
            $this->closeStatus($parameters['chat_id'], "Спасибо! Обьект успешно загружен в базу данных. После проверки он будет доступен в боте. Функции бота снова доступны.", false);
            return;
        } else {
            if (!$this->estate_facilities[$stage_members['put']]->put($parameters, $this->getObjectId($parameters['chat_id']))) { return; }
            $this->estate_facilities[$stage_members['ask']]->ask($parameters);
        }

        $this->changeStatusStage($nextStage, $parameters['chat_id']);
    }

    function createEstateObject($parameters)
    {
        $this->db->query("INSERT INTO estate_objects (`owner_id`) VALUES (?)", [$parameters['chat_id']]);
        $object_id = $this->db->query("SELECT LAST_INSERT_ID()")->find()['LAST_INSERT_ID()'];
        $this->db->query("UPDATE input_estate SET object_id=? WHERE user_id=?", [$object_id, $parameters['chat_id']]);
    }

    function closeStatus($chat_id, $message_to_user, $is_emergency)
    {
        $this->telegram->sendMessage([
            'chat_id' => $chat_id,
            'text'=> $message_to_user,
        ]);

        if ($is_emergency) {
            // УДАЛЕНИЕ НЕДОВВЕДЕННОГО ОБЬЕКТА
            $this->db->query("DELETE FROM estate_objects WHERE id=?", [$this->getObjectId($chat_id)]);
        } else {
            // ЗАКРЫТИЕ ВИДИМОСТИ ОБЬЕКТА И ОТПРАВКА В ОТДЕЛ МОДЕРАЦИИ
            $this->db->query("UPDATE estate_objects SET display=? WHERE id=?", [0, $this->getObjectId($chat_id)]);
            $this->moderation->informModerator($this->getObjectId($chat_id));
        }

        // УДАЛЕНИЕ ЗАПИСИ ИЗ input_estate
        $this->db->query("DELETE FROM input_estate WHERE user_id=?", [$chat_id]);

        // ЗАКРЫТИЕ СТАТУСА ЗАГРУЗКИ ОБЬЕКТА
        $this->db->query("UPDATE users SET status=? WHERE id=?", ['nothing', $chat_id]);         
    }

    function getStatusStage($chat_id)
    {
        if ($this->db->query("SELECT * FROM input_estate WHERE user_id=?", [$chat_id])->find()) {
            return $this->db->query("SELECT * FROM input_estate WHERE user_id=?", [$chat_id])->find()['stage'];
        } 
    }

    function getObjectId($chat_id)
    {
        if ($this->db->query("SELECT * FROM input_estate WHERE user_id=?", [$chat_id])->find()) {
            return $this->db->query("SELECT * FROM input_estate WHERE user_id=?", [$chat_id])->find()['object_id'];
        } 
    }

    function changeStatusStage($status_stage, $chat_id)
    {
        if ($this->db->query("SELECT * FROM input_estate WHERE user_id=?", [$chat_id])->find()) {
            $this->db->query("UPDATE input_estate SET stage=? WHERE user_id=?", [$status_stage, $chat_id]);
        } 
    }

}