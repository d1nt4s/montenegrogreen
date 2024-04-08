<?php

return [

'entering_object_type' => [
    'inline_keyboard' => [
        [ ['text' => $phrases['type_house'], 'callback_data' => 'type_house']], 
        [['text' => $phrases['type_flat'], 'callback_data' => 'type_flat']],
        [['text' => $phrases['type_hotel'], 'callback_data' => 'type_hotel']], 
        [['text' => $phrases['type_land'], 'callback_data' => 'type_land']], 
        [['text' => $phrases['type_commercy'], 'callback_data' => 'type_commercy'] ],
        // [['text' => $phrases['add_object_continue'], 'callback_data' => '78771_continue'] ],
    ],
],

'entering_object_city' => [
    'inline_keyboard' => [
        [['text' => $phrases['city_budva'], 'callback_data' => 'city_budva']], 
        [['text' => $phrases['city_bar'], 'callback_data' => 'city_bar']], 
        [['text' => $phrases['city_tivat'], 'callback_data' => 'city_tivat']],
        [['text' => $phrases['city_podgorica'], 'callback_data' => 'city_podgorica']],
        [['text' => $phrases['city_herzegnovi'], 'callback_data' => 'city_herzegnovi']],
        [['text' => $phrases['city_sutomore'], 'callback_data' => 'city_sutomore']],
    ],
],

'show_new_object' => [
    'inline_keyboard' => [
        [['text' => "Покажи!", 'callback_data' => 'moderator_show_new_object']], 
    ],
],


];