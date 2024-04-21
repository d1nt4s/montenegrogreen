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
        if ($this->getAction($parameters['chat_id']) !== 'nothing') {
            switch ($this->getAction($parameters['chat_id']))
            {
                case ('menu'):
                    $this->showMainMenu($parameters);
                    break;
                case ('edit'):
                    $this->facilityPut($parameters);
                    break;
            }
        }
        /* Или Выполнение этапа по коллбэку */
        else if ($parameters['isCallback']) 
        {
            $this->telegram->answerCallbackQuery(['show_alert' => true, 'callback_query_id' => $parameters['id'], ]);

            if (str_contains($parameters['message_text'], 'EDIT_ESTATE_CHANGE_FACILITY')) {
                $this->showEstateFacilitiesMenu($parameters);
            } else if (str_contains($parameters['message_text'], 'EDIT_ESTATE_CHANGE_')) {
                $this->facilityAsk($parameters);
            } else if (str_contains($parameters['message_text'], 'EDIT_ESTATE_HIDE_OBJECT')) {
                $this->hideObject($parameters);
            } else if (str_contains($parameters['message_text'], 'EDIT_ESTATE_DELETE_OBJECT')) {
                $this->deleteObjectByUser($parameters);
            } else if (str_contains($parameters['message_text'], 'EDIT_ESTATE_CLOSE_STATUS')) {
                $this->closeStatus($parameters);
            }
        }
    }

    function facilityAsk($parameters)
    {
        $facility_name = substr($parameters['message_text'], 19);

        $facility_full_name = '\Dantes\Montegreen\Statuses\Estate\Input\\' . $facility_name;
        $facility = new $facility_full_name($this->telegram, $this->db, get_keyboard('input_estate'));
        $facility->ask($parameters);

        $this->putAction($parameters['chat_id'], 'edit');        
        /* Сохранение названия характеристики в базе данных */
        $this->db->query("UPDATE edit_estate SET estate_facility=? WHERE user_id=?", [$facility_name, $parameters['chat_id']]);
    }

    function facilityPut($parameters)
    {
        /* Получение название характеристики и object_id из базы данных */
        if ($this->db->query("SELECT * FROM edit_estate WHERE user_id=?", [$parameters['chat_id']])->find()) {
            $data = $this->db->query("SELECT * FROM edit_estate WHERE user_id=?", [$parameters['chat_id']])->find();
            $facility_name = $data['estate_facility'];
            $object_id = $data['object_id'];
        } 

        $facility_name = '\Dantes\Montegreen\Statuses\Estate\Input\\' . $facility_name;
        $facility = new $facility_name($this->telegram, $this->db, get_keyboard('input_estate'));
        if(!$facility->put($parameters, $object_id)) {
            return;
        } 

        /* Удаление названия измененной характеристики из базы */
        $this->db->query("UPDATE edit_estate SET estate_facility=? WHERE user_id=?", ['NULL', $parameters['chat_id']]);
        /* Рестарт бота, выключение в process(), 'menu' уводит обратно в меню */
        $this->putAction($parameters['chat_id'], 'menu');
        $GLOBALS['restart_bot'] = true;
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

    function hideObject($parameters)
    {
        if ($this->db->query("SELECT * FROM edit_estate WHERE user_id=?", [$parameters['chat_id']])->find()) {
            $object_id = $this->db->query("SELECT * FROM edit_estate WHERE user_id=?", [$parameters['chat_id']])->find()['object_id'];
        } 

        if ($this->db->query("SELECT * FROM estate_objects WHERE id=?", [$object_id])->find()) {
            $this->db->query("UPDATE estate_objects SET display=? WHERE id=?", [0, $object_id]);    
        }  

        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=>  "Ваш обьект больше не показывается людям!",
        ]);

        /* Рестарт бота, выключение в process(), 'menu' уводит обратно в меню */
        $this->putAction($parameters['chat_id'], 'menu');
        $GLOBALS['restart_bot'] = true;
    }

    function deleteObjectByUser($parameters)
    {
        if ($this->db->query("SELECT * FROM edit_estate WHERE user_id=?", [$parameters['chat_id']])->find()) {
            $object_id = $this->db->query("SELECT * FROM edit_estate WHERE user_id=?", [$parameters['chat_id']])->find()['object_id'];
        } 

        // УДАЛЕНИЕ ОБЬЕКТА из estate_objects
        $this->db->query("DELETE FROM estate_objects WHERE id=? AND owner_id=?", [$object_id ,$parameters['chat_id']]);

        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=>  "Вашего обьекта с id={$object_id} больше нет!",
        ]);

        $this->closeStatus($parameters);
    }

    function closeStatus($parameters)
    {
        if ($this->db->query("SELECT * FROM edit_estate WHERE user_id=?", [$parameters['chat_id']])->find()) {
            $object_id = $this->db->query("SELECT * FROM edit_estate WHERE user_id=?", [$parameters['chat_id']])->find()['object_id'];
        } 

        // УДАЛЕНИЕ ЗАПИСИ ИЗ edit_estate
        $this->db->query("DELETE FROM edit_estate WHERE user_id=?", [$parameters['chat_id']]);

        // ЗАКРЫТИЕ СТАТУСА в users
        $this->db->query("UPDATE users SET status=? WHERE id=?", ['nothing', $parameters['chat_id']]);        

        $this->telegram->sendMessage([
            'chat_id' => $parameters['chat_id'],
            'text'=>  "Выход из состояния управления обьектом {$object_id}",
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