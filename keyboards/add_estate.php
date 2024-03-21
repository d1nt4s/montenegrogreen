<?php

return [

    'instruction' => [
        'inline_keyboard' => [
            [['text' => $phrases['add_object_continue'], 'callback_data' => 'ADD_ESTATE_CONTINUE']],
        ],
    ],

    'buy' => [
        'inline_keyboard' => [
            [['text' => $phrases['buy_object'], 'callback_data' => 'ADD_ESTATE_BUY_OBJECT_PLACES']],
        ],
    ],

    'input_or_buy' => [
        'inline_keyboard' => [
            [['text' => $phrases['add_object'], 'callback_data' => 'ADD_ESTATE_INPUT_NEW_OBJECT']],
            [['text' => $phrases['buy_object'], 'callback_data' => 'ADD_ESTATE_BUY_OBJECT_PLACES']],
        ],
    ],

    'input_one_more' => [
        'inline_keyboard' => [
            [['text' => $phrases['add_one_more_object'], 'callback_data' => 'ADD_ESTATE_INPUT_NEW_OBJECT']],
        ],
    ],
];