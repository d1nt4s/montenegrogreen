<?php

return [

    'menu' => [
        'inline_keyboard' => [
            [['text' => $phrases['manage_estate_edit'], 'callback_data' => 'MANAGE_ESTATE_EDIT']],
            [['text' => $phrases['manage_estate_delete'], 'callback_data' => 'MANAGE_ESTATE_DELETE']],
        ],
    ],

    'attention' => [
        'inline_keyboard' => [
            [['text' => $phrases['manage_estate_yes'], 'callback_data' => 'MANAGE_ESTATE_DELETE_OBJECT']],
            [['text' => $phrases['manage_estate_no'], 'callback_data' => 'MANAGE_ESTATE_LEAVE_OBJECT']],
        ],
    ],

    // 'instruction' => [
    //     'inline_keyboard' => [
    //         [['text' => $phrases['add_object_continue'], 'callback_data' => 'ADD_ESTATE_CONTINUE']],
    //     ],
    // ],

    // 'buy' => [
    //     'inline_keyboard' => [
    //         [['text' => $phrases['buy_object'], 'callback_data' => 'ADD_ESTATE_BUY_OBJECT_PLACES']],
    //     ],
    // ],

    // 'input_or_buy' => [
    //     'inline_keyboard' => [
    //         [['text' => $phrases['add_object'], 'callback_data' => 'ADD_ESTATE_INPUT_NEW_OBJECT']],
    //         [['text' => $phrases['buy_object'], 'callback_data' => 'ADD_ESTATE_BUY_OBJECT_PLACES']],
    //     ],
    // ],

    // 'input_one_more' => [
    //     'inline_keyboard' => [
    //         [['text' => $phrases['add_one_more_object'], 'callback_data' => 'ADD_ESTATE_INPUT_NEW_OBJECT']],
    //     ],
    // ],
];