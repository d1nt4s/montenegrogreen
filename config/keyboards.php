<?php

return [

'base_keyboard' => [
    'keyboard' => [
        [ $phrases['setup'], $phrases['list']],
        [ $phrases['filter'], $phrases['showall']],
    ],
    'resize_keyboard' => true,
],

'house_filter' => [
    'inline_keyboard' => [
        [ ['text' => $phrases['type_house'], 'callback_data' => 'type_house'], ['text' => $phrases['type_flat'], 'callback_data' => 'type_flat'] ],
        [ ['text' => $phrases['type_both'], 'callback_data' => 'type_both'] ],
    ],
],

'city_filter_1' => [
    'inline_keyboard' => [
        [ ['text' => $phrases['city_budva'], 'callback_data' => 'city_budva'], ['text' => $phrases['city_bar'], 'callback_data' => 'city_bar'], ['text' => $phrases['city_tivat'], 'callback_data' => 'city_tivat'] ],
        [ ['text' => $phrases['city_page_2'], 'callback_data' => 'city_page_2'], ['text' => $phrases['city_next_stage'], 'callback_data' => 'city_next_stage'] ],
    ],
],

'city_filter_2' => [
    'inline_keyboard' => [
        [ ['text' => $phrases['city_podgorica'], 'callback_data' => 'city_podgorica'], ['text' => $phrases['city_herzegnovi'], 'callback_data' => 'city_herzegnovi'], ['text' => $phrases['city_sutomore'], 'callback_data' => 'city_sutomore'] ],
        [ ['text' => $phrases['city_page_1'], 'callback_data' => 'city_page_1'], ['text' => $phrases['city_next_stage'], 'callback_data' => 'city_next_stage'] ],
    ],
],

'price_filter' => [
    'inline_keyboard' => [
        [ ['text' => $phrases['price_value_1'], 'callback_data' => 'price_value_1'], ['text' => $phrases['price_value_2'], 'callback_data' => 'price_value_2'], ['text' => $phrases['price_value_3'], 'callback_data' => 'price_value_3'] ],
        [ ['text' => $phrases['price_value_4'], 'callback_data' => 'price_value_4'], ['text' => $phrases['price_value_5'], 'callback_data' => 'price_value_5'], ['text' => $phrases['price_value_your'], 'callback_data' => 'price_value_your'] ],
    ],
],
];