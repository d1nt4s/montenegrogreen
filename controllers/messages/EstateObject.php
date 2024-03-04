<?php
class EstateObject
{
    private $telegram;
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

    function __construct($telegram, $keyboards)
    {
        $this->telegram = $telegram;
        $this->keyboards = $keyboards;
    }

    function can_handle($text)
    {
        if ($text == '/list' || $text == 'Список обьектов') {
            $this->getType();
        } elseif (str_contains($text, '6556type_')) { // ТУТ ДОЛЖНО БЫТЬ КАКОЕ ТО ОСОБЕННОЕ КОДОВОЕ ИМЯ, КОТОРОЕ НЕЛЬЗЯ ПОДОБРАТЬ РУКАМИ ИЛИ ПРИДУМАТЬ, КОДОВОЕ ИМЯ СОДЕРЖИТ КЛАСС-СТАДИЮ-ЕЩЕ ЧТО-ТО
            $this->getCity();
        } elseif (str_contains($text, '4732city_')) { // ТУТ ДОЛЖНО БЫТЬ КАКОЕ ТО ОСОБЕННОЕ КОДОВОЕ ИМЯ, КОТОРОЕ НЕЛЬЗЯ ПОДОБРАТЬ РУКАМИ ИЛИ ПРИДУМАТЬ, КОДОВОЕ ИМЯ СОДЕРЖИТ КЛАСС-СТАДИЮ-ЕЩЕ ЧТО-ТО
            $this->getPrice();
        } elseif (str_contains($text, '9921price_')) { // ТУТ ДОЛЖНО БЫТЬ КАКОЕ ТО ОСОБЕННОЕ КОДОВОЕ ИМЯ, КОТОРОЕ НЕЛЬЗЯ ПОДОБРАТЬ РУКАМИ ИЛИ ПРИДУМАТЬ, КОДОВОЕ ИМЯ СОДЕРЖИТ КЛАСС-СТАДИЮ-ЕЩЕ ЧТО-ТО
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

    function getType()
    {
        // ИЗМЕНЕНИЕ КЛАВИАТУР - ПРОСТАВКА ГАЛОЧЕК - ТОЖЕ САМОЕ СВЯЗАННОЕ С ГОРОДАМИ И ЦЕНОЙ
    }

    function getCity()
    {

    }

    function getPrice()
    {

    }

}