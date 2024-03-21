<?php

return [

'base_keyboard' => [
    'keyboard' => [
        [ $phrases['setup'], $phrases['list']],
        [ $phrases['filter'], $phrases['showall']],
    ],
    'resize_keyboard' => true,
],

'object_type' => [
    'inline_keyboard' => [
        [ ['text' => $phrases['type_house'], 'callback_data' => '6556type_house']], 
        [['text' => $phrases['type_flat'], 'callback_data' => '6556type_flat']],
        [['text' => $phrases['type_hotel'], 'callback_data' => '6556type_hotel']], 
        [['text' => $phrases['type_land'], 'callback_data' => '6556type_land']], 
        [['text' => $phrases['type_commercy'], 'callback_data' => '6556type_commercy'] ],
        [['text' => $phrases['next_stage'], 'callback_data' => '65561type_next_stage'] ],
    ],
],

'object_city' => [
    'inline_keyboard' => [
        [[ 'text' => $phrases['city_budva'], 'callback_data' => '4732city_budva']], 
        [['text' => $phrases['city_bar'], 'callback_data' => '4732city_bar']], 
        [['text' => $phrases['city_tivat'], 'callback_data' => '4732city_tivat']],
        [['text' => $phrases['city_podgorica'], 'callback_data' => '4732city_podgorica']],
        [['text' => $phrases['city_herzegnovi'], 'callback_data' => '4732city_herzegnovi']],
        [['text' => $phrases['city_sutomore'], 'callback_data' => '4732city_sutomore']],
        [['text' => $phrases['next_stage'], 'callback_data' => '47321city_next_stage'] ],
    ],
],

'object_price' => [
    'inline_keyboard' => [
        [['text' => $phrases['price_0-50'], 'callback_data' => '9921price_0-50']],
        [['text' => $phrases['price_50-100'], 'callback_data' => '9921price_50-100']], 
        [['text' => $phrases['price_100-200'], 'callback_data' => '9921price_100-200']],
        [['text' => $phrases['price_200-350'], 'callback_data' => '9921price_200-350']], 
        [['text' => $phrases['price_350-infinity'], 'callback_data' => '9921price_350-infinity']], 
        [['text' => $phrases['match'], 'callback_data' => '99211price_match'] ],
        // ['text' => $phrases['price_value_your'], 'callback_data' => 'price_value_your'] ],
    ],
],

'add_object_continue' => [
    'inline_keyboard' => [
        [['text' => $phrases['add_object_continue'], 'callback_data' => '12add_object_continue']],
    ],
],

'buy_object' => [
    'inline_keyboard' => [
        [['text' => $phrases['buy_object'], 'callback_data' => '53buy_object']],
    ],
],

'add_and_buy_object' => [
    'inline_keyboard' => [
        [['text' => $phrases['add_object'], 'callback_data' => '34add_object']],
        [['text' => $phrases['buy_object'], 'callback_data' => '53buy_object']],
    ],
],

'add_one_more_object' => [
    'inline_keyboard' => [
        [['text' => $phrases['add_one_more_object'], 'callback_data' => '66add_one_more_object']],
    ],
],

'entering_object_type' => [
    'inline_keyboard' => [
        [ ['text' => $phrases['type_house'], 'callback_data' => '7877type_house']], 
        [['text' => $phrases['type_flat'], 'callback_data' => '7877type_flat']],
        [['text' => $phrases['type_hotel'], 'callback_data' => '7877type_hotel']], 
        [['text' => $phrases['type_land'], 'callback_data' => '7877type_land']], 
        [['text' => $phrases['type_commercy'], 'callback_data' => '7877type_commercy'] ],
        [['text' => $phrases['add_object_continue'], 'callback_data' => '78771_continue'] ],
    ],
],

];