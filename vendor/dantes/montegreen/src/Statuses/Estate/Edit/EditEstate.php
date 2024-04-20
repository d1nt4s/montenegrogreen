<?php

namespace Dantes\Montegreen\Statuses\Estate\Edit;

class EditEstate
{
    public $status_handler_name = "edit";
    private $telegram;
    private $db;
    protected $keyboards;

    function __construct($telegram, $db)
    {
        $this->telegram = $telegram;
        $this->db = $db;

        require_once $GLOBALS['paths']['config'] . '/include.php';
        $this->keyboards = get_keyboard('edit_estate');
    }

    public function process($parameters)
    {
        $GLOBALS['restart_bot'] = false;

        /* Выполнение этапа по action из базы данных */
        switch ($this->getAction($parameters['chat_id']))
        {
            case ('menu'):
                $this->showMainMenu($parameters);
                break;
            case ('edit'):
                $this->facilityPut($parameters);
                break;
        }

        /* Выполнение этапа по коллбэку */
        if ($parameters['isCallback']) 
        {
            $this->telegram->answerCallbackQuery(['show_alert' => true, 'callback_query_id' => $parameters['id'], ]);

            if (str_contains($parameters['message_text'], 'EDIT_ESTATE_CHANGE_FACILITY')) {
                $this->showEstateFacilitiesMenu($parameters);
            } else if (str_contains($parameters['message_text'], 'EDIT_ESTATE_CHANGE_')) {
                $this->facilityAsk($parameters);
            } else if (str_contains($parameters['message_text'], 'EDIT_ESTATE_HIDE_OBJECT')) {

            } else if (str_contains($parameters['message_text'], 'EDIT_ESTATE_DELETE_OBJECT')) {

            }
        }
    }

    function facilityAsk($parameters)
    {
        $facility_name = substr($parameters['message_text'], 19);

        $this->putAction($parameters['chat_id'], 'edit');        
        /* Сохранение названия характеристики в базе данных */
        $this->db->query("UPDATE edit_estate SET estate_facility=? WHERE user_id=?", [$facility_name, $parameters['chat_id']]);

        require_once $GLOBALS['paths']['config'] . '/include.php';

        $facility_name = '\Dantes\Montegreen\Statuses\Estate\Input\\' . $facility_name;
        $facility = new $facility_name($this->telegram, $this->db, get_keyboard('input_estate'));
        $facility->ask($parameters);
    }

    function facilityPut($parameters)
    {
        /* Получение название характеристики и object_id из базы данных */
        if ($this->db->query("SELECT * FROM edit_estate WHERE user_id=?", [$parameters['chat_id']])->find()) {
            $data = $this->db->query("SELECT * FROM edit_estate WHERE user_id=?", [$parameters['chat_id']])->find();
            $facility_name = $data['estate_facility'];
            $object_id = $data['object_id'];
        } 

        require_once $GLOBALS['paths']['config'] . '/include.php';

        $facility_name = '\Dantes\Montegreen\Statuses\Estate\Input\\' . $facility_name;
        $facility = new $facility_name($this->telegram, $this->db, get_keyboard('input_estate'));
        $facility->put($parameters, $object_id); 
    }
    
    function showMainMenu($parameters)
    {
        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=>  "Что вы хотите сделать с обьектом?",
            'reply_markup' => new \Telegram\Bot\Keyboard\Keyboard($this->keyboards['main_menu']),
        ]);

        $this->clearAction($parameters['chat_id']);
    }

    function showEstateFacilitiesMenu($parameters)
    {
        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=>  "Какую характеристику обьекта вы желаете изменить?",
            'reply_markup' => new \Telegram\Bot\Keyboard\Keyboard($this->keyboards['estate_facilities_menu']),
        ]);
    }

    function putAction($chat_id, $action)
    {
        if ($this->db->query("SELECT * FROM edit_estate WHERE user_id=?", [$chat_id])->find()) {
            $this->db->query("UPDATE edit_estate SET action=? WHERE user_id=?", [$action, $chat_id]);    
        } 
    }
    function clearAction($chat_id)
    {
        if ($this->db->query("SELECT * FROM edit_estate WHERE user_id=?", [$chat_id])->find()) {
            $this->db->query("UPDATE edit_estate SET action=? WHERE user_id=?", ['nothing', $chat_id]);    
        } 
    }
    function getAction($chat_id)
    {
        if ($this->db->query("SELECT * FROM edit_estate WHERE user_id=?", [$chat_id])->find()) {
            return $this->db->query("SELECT * FROM edit_estate WHERE user_id=?", [$chat_id])->find()['action'];
        } 
    }
}