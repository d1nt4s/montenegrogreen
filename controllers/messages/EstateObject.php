<?php
class EstateObject
{
    private $telegram;
    private $db;
    private $categories = [
        'type' => [
            '6556type_house' => false,
            '6556type_flat' => false,
            '6556type_hotel' => false,
            '6556type_land' => false,
            '6556type_commercy' => false,
        ],
        'city' => [
            '4732city_budva' => false,
            '4732city_bar' => false,
            '4732city_tivat' => false,
            '4732city_podgorica' => false,
            '4732city_herzegnovi' => false,
            '4732city_sutomore' => false,
        ],
        'price' => [
            '9921price_0-75' => false,
            '9921price_75-125' => false,
            '9921price_125-200' => false,
            '9921price_200-350' => false,
            '9921price_350-infinity' => false,
        ],
    ];

    public $keyboards;

    function __construct($telegram, $keyboards, $db)
    {
        $this->telegram = $telegram;
        $this->keyboards = $keyboards;
        $this->db = $db;
    }

    function can_handle($parameters)
    {
        if ($parameters['message_text'] == '/list' || $parameters['message_text'] == 'Список обьектов' || str_contains($parameters['message_text'], '6556type_')) {
            $this->getType($parameters);
        } elseif (str_contains($parameters['message_text'], '65561type_next_stage') || str_contains($parameters['message_text'], '4732city_')) { // ТУТ ДОЛЖНО БЫТЬ КАКОЕ ТО ОСОБЕННОЕ КОДОВОЕ ИМЯ, КОТОРОЕ НЕЛЬЗЯ ПОДОБРАТЬ РУКАМИ ИЛИ ПРИДУМАТЬ, КОДОВОЕ ИМЯ СОДЕРЖИТ КЛАСС-СТАДИЮ-ЕЩЕ ЧТО-ТО
            die;
            $this->getCity();
        } elseif (str_contains($parameters['message_text'], '47321city_next_stage')) { // ТУТ ДОЛЖНО БЫТЬ КАКОЕ ТО ОСОБЕННОЕ КОДОВОЕ ИМЯ, КОТОРОЕ НЕЛЬЗЯ ПОДОБРАТЬ РУКАМИ ИЛИ ПРИДУМАТЬ, КОДОВОЕ ИМЯ СОДЕРЖИТ КЛАСС-СТАДИЮ-ЕЩЕ ЧТО-ТО
            $this->getPrice();
        } elseif (str_contains($parameters['message_text'], '9921price_')) { // ТУТ ДОЛЖНО БЫТЬ КАКОЕ ТО ОСОБЕННОЕ КОДОВОЕ ИМЯ, КОТОРОЕ НЕЛЬЗЯ ПОДОБРАТЬ РУКАМИ ИЛИ ПРИДУМАТЬ, КОДОВОЕ ИМЯ СОДЕРЖИТ КЛАСС-СТАДИЮ-ЕЩЕ ЧТО-ТО
            // показ списка обьектов (отправка true + что-то для перехода в handle() )
            
        } else {
            return false;
        }

        return true;
        // } elseif (str_contains($text, '')) { // ТУТ ДОЛЖНО БЫТЬ КАКОЕ ТО ОСОБЕННОЕ КОДОВОЕ ИМЯ, КОТОРОЕ НЕЛЬЗЯ ПОДОБРАТЬ РУКАМИ ИЛИ ПРИДУМАТЬ, КОДОВОЕ ИМЯ СОДЕРЖИТ КЛАСС-СТАДИЮ-ЕЩЕ ЧТО-ТО

        // }
    }

    function handle($chat_id)
    {
        // if (...) // проверка что фильтрация закончилась
    }

    function show_single()
    {

    }

    function show_list()
    {

    }

    function getType($parameters)
    {
        // ИЗМЕНЕНИЕ КЛАВИАТУР - ПРОСТАВКА ГАЛОЧЕК - ТОЖЕ САМОЕ СВЯЗАННОЕ С ГОРОДАМИ И ЦЕНОЙ
        if (!$parameters['isCallback']) {

            $this->telegram->sendMessage([
                'chat_id' => $parameters['chat_id'],
                'text'=>  "Выберите тип недвижимости ниже и нажмите 'Следующий раздел'",
                'reply_markup' => new Telegram\Bot\Keyboard\Keyboard($this->keyboards['object_type']),
            ]);

        } else {

            // ОТВЕЧАЕТ НА callback
            $this->telegram->answerCallbackQuery([
                'show_alert' => true,
                'callback_query_id' => $parameters['id'],
            ]);

            // проверить если ли уже обьект в базе данных и если есть удалить его оттуда, иначе добавить 
            if ($this->db->query("SELECT * FROM user_choices WHERE user_id=? AND type=? AND choice=?", [$parameters['chat_id'], 'type', $parameters['message_text']])->find()) {
                $this->db->query("DELETE FROM user_choices WHERE user_id=? AND type=? AND choice=?", [$parameters['chat_id'], 'type', $parameters['message_text']]);
            } else {
            
                // Проверка на идентичность записываемых строк, вдруг такое предпочтение уже создано? Проверь, может оно и не нужно и все они удаляются в конце фильтрации
                $this->db->query("INSERT INTO user_choices (`user_id`, `type`, `choice`) VALUES (?, ?, ?)", [$parameters['chat_id'], 'type', $parameters['message_text']]);

            }

            // вывод и обработка ранее выбранных вариантов
            $res = $this->db->query("SELECT * FROM user_choices WHERE user_id=? AND type=?", [$parameters['chat_id'], 'type'])->findAll();

            $choices = [];
            foreach($res as $array) {
                if (!in_array($array['choice'], $choices)) {
                    array_push($choices, $array['choice']);
                }
            }

            // ОБРАБОТКА КЛАВИАТУРЫ В СООТВЕТСТВИИ С выборами пользователя
            $counter = 0;
            foreach($this->keyboards['object_type']['inline_keyboard'][0] as $choice) {
                // if ($choice['callback_data'] == $parameters['message_text']) {
                if (in_array($choice['callback_data'], $choices)) {
                    if (substr($this->keyboards['object_type']['inline_keyboard'][0][$counter]['text'], -1) !== '✅')
                        $this->keyboards['object_type']['inline_keyboard'][0][$counter]['text'] .= '✅';
                    // else
                        // substr($this->keyboards['object_type']['inline_keyboard'][0][$counter]['text'], 0, -1); 
                }
                $counter++;
            }

            // Изменение клавиаутуры подстать выбранным категориям пользователя
            $this->telegram->editMessageText([
                'chat_id' => $parameters['chat_id'],
                'message_id' => $parameters['message_id'],
                'text' => "Ну Выберите тип недвижимости ниже и нажмите 'Следующий раздел'",
                'parse_mode' => 'HTML',
                'reply_markup' => new Telegram\Bot\Keyboard\Keyboard($this->keyboards['object_type']),
            ]);
            
        }

    }

    function getCity()
    {

    }

    function getPrice()
    {

    }

}