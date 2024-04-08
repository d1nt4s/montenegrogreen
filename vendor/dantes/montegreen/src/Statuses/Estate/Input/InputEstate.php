<?php

namespace Dantes\Montegreen\Statuses\Estate\Input;

class InputEstate
{
    public $status_handler_name = "input";
    private $telegram;
    private $db;
    protected $name;
    protected $type;
    protected $city;
    protected $house_size;
    protected $region_size;
    protected $price;
    protected $rooms;
    protected $contacts;
    protected $link;
    protected $description;
    protected $moderation;
    function __construct($telegram, $db)
    {
        $this->telegram = $telegram;
        $this->db = $db;
        require_once $GLOBALS['paths']['config'] . '/include.php';
        $keyboards = get_keyboard('input_estate');

        $this->name = new Name($telegram, $db);
        $this->type = new Type($telegram, $db, $keyboards);
        $this->city = new City($telegram, $db, $keyboards);
        $this->house_size = new HouseSize($telegram, $db);
        $this->region_size = new RegionSize($telegram, $db);
        $this->price = new Price($telegram, $db);
        $this->rooms = new Rooms($telegram, $db);
        $this->contacts = new Contacts($telegram, $db);
        $this->link = new Link($telegram, $db);
        $this->description = new Description($telegram, $db);

        $this->moderation = new Moderation($telegram, $db, $keyboards);
    }

    public function process($parameters)
    {
        $GLOBALS['restart_bot'] = false;

        if ($parameters['message_text'] === '/stop') {
            $this->emergencyCloseStatus($parameters['chat_id']);
        } else {
            $this->telegram->sendMessage([
                'chat_id' => $parameters['chat_id'],
                'text'=> 'Статус загрузки обьекта! Для прерывания введите /stop, место при этом не потратиться, однако введенные данные не запомняться!',
            ]);
        }

        switch ($this->getStatusStage($parameters['chat_id']))
        {
            case ('create_ask_name'):
                $this->createEstateObject($parameters);
                $this->name->ask($parameters);
                $this->changeStatusStage('put_name_ask_type', $parameters['chat_id']);
                break;
            case ('put_name_ask_type'):
                $this->name->put($parameters, $this->getObjectId($parameters['chat_id']));
                $this->type->ask($parameters);
                $this->changeStatusStage('put_type_ask_city', $parameters['chat_id']);
                break;
            case ('put_type_ask_city'):
                $this->type->put($parameters, $this->getObjectId($parameters['chat_id']));
                $this->city->ask($parameters);
                $this->changeStatusStage('put_city_ask_housesize', $parameters['chat_id']);
                break;
            case ('put_city_ask_housesize'):
                $this->city->put($parameters, $this->getObjectId($parameters['chat_id']));
                $this->house_size->ask($parameters);
                $this->changeStatusStage('put_housesize_ask_regionsize', $parameters['chat_id']);
                break;
            case ('put_housesize_ask_regionsize'):
                $this->house_size->put($parameters, $this->getObjectId($parameters['chat_id']));
                $this->region_size->ask($parameters);
                $this->changeStatusStage('put_regionsize_ask_price', $parameters['chat_id']);
                break;
            case ('put_regionsize_ask_price'):
                $this->region_size->put($parameters, $this->getObjectId($parameters['chat_id']));
                $this->price->ask($parameters);
                $this->changeStatusStage('put_price_ask_rooms', $parameters['chat_id']);
                break;
            case ('put_price_ask_rooms'):
                $this->price->put($parameters, $this->getObjectId($parameters['chat_id']));
                $this->rooms->ask($parameters);
                $this->changeStatusStage('put_rooms_ask_contacts', $parameters['chat_id']);
                break;
            case ('put_rooms_ask_contacts'):
                $this->rooms->put($parameters, $this->getObjectId($parameters['chat_id']));
                $this->contacts->ask($parameters);
                $this->changeStatusStage('put_contacts_ask_link', $parameters['chat_id']);
                break;
            case ('put_contacts_ask_link'):
                $this->contacts->put($parameters, $this->getObjectId($parameters['chat_id']));
                $this->link->ask($parameters);
                $this->changeStatusStage('put_link_ask_description', $parameters['chat_id']);
                break;
            case ('put_link_ask_description'):
                $this->link->put($parameters, $this->getObjectId($parameters['chat_id']));
                $this->description->ask($parameters);
                $this->changeStatusStage('put_description_finish', $parameters['chat_id']);
                break;
            case ('put_description_finish'):
                $this->description->put($parameters, $this->getObjectId($parameters['chat_id']));
                $this->closeStatus($parameters['chat_id']);
                break;
        }

    }

    function createEstateObject($parameters)
    {
        $this->db->query("INSERT INTO estate_objects (`owner_id`) VALUES (?)", [$parameters['chat_id']]);
        $object_id = $this->db->query("SELECT LAST_INSERT_ID()")->find()['LAST_INSERT_ID()'];
        $this->db->query("UPDATE input_estate SET object_id=? WHERE user_id=?", [$object_id, $parameters['chat_id']]);
    }
    function closeStatus($chat_id)
    {
        $this->telegram->sendMessage([
            'chat_id' => $chat_id,
            'text'=> 'Спасибо! Обьект успешно загружен в базу данных. После проверки он будет доступен в боте. Функции бота снова доступны.',
        ]);

        // ЗАКРЫТИЕ ВИДИМОСТИ ОБЬЕКТА И ОТПРАВКА В ОТДЕЛ МОДЕРАЦИИ
        $this->db->query("UPDATE estate_objects SET display=? WHERE id=?", [0, $this->getObjectId($chat_id)]);
        $this->moderation->informModerator($this->getObjectId($chat_id));

        // УДАЛЕНИЕ ЗАПИСИ ИЗ input_estate
        $this->db->query("DELETE FROM input_estate WHERE user_id=?", [$chat_id]);

        // ЗАКРЫТИЕ СТАТУСА ЗАГРУЗКИ ОБЬЕКТА
        $this->db->query("UPDATE users SET status=? WHERE id=?", ['nothing', $chat_id]); 
        
    }

    function emergencyCloseStatus($chat_id)
    {
        $this->telegram->sendMessage([
            'chat_id' => $chat_id,
            'text'=> 'Выход из статуса загрузки обьекта. Все функции бота снова доступны.',
        ]);

        // УДАЛЕНИЕ НЕДОВВЕДЕННОГО ОБЬЕКТА
        $this->db->query("DELETE FROM estate_objects WHERE id=?", [$this->getObjectId($chat_id)]);

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