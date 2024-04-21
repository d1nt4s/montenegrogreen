<?php

return [

'main_menu' => [
    'inline_keyboard' => [
        [['text' => $phrases['edit_estate_change_facility'], 'callback_data' => 'EDIT_ESTATE_CHANGE_FACILITY']], 
        [['text' => $phrases['edit_estate_hide_object'], 'callback_data' => 'EDIT_ESTATE_HIDE_OBJECT']], 
        [['text' => $phrases['edit_estate_delete_object'], 'callback_data' => 'EDIT_ESTATE_DELETE_OBJECT']],
        [['text' => $phrases['edit_estate_close_status'], 'callback_data' => 'EDIT_ESTATE_CLOSE_STATUS']],
    ],
],

'estate_facilities_menu' => [
    'inline_keyboard' => [
        [ ['text' => $phrases['edit_estate_change_name'], 'callback_data' => 'EDIT_ESTATE_CHANGE_Name']], 
        [['text' => $phrases['edit_estate_change_type'], 'callback_data' => 'EDIT_ESTATE_CHANGE_Type']],
        [['text' => $phrases['edit_estate_change_city'], 'callback_data' => 'EDIT_ESTATE_CHANGE_City']], 
        [['text' => $phrases['edit_estate_change_housesize'], 'callback_data' => 'EDIT_ESTATE_CHANGE_HouseSize']], 
        [['text' => $phrases['edit_estate_change_regionsize'], 'callback_data' => 'EDIT_ESTATE_CHANGE_RegionSize'] ],
        [['text' => $phrases['edit_estate_change_price'], 'callback_data' => 'EDIT_ESTATE_CHANGE_Price'] ],
        [['text' => $phrases['edit_estate_change_rooms'], 'callback_data' => 'EDIT_ESTATE_CHANGE_Rooms'] ],
        [['text' => $phrases['edit_estate_change_contacts'], 'callback_data' => 'EDIT_ESTATE_CHANGE_Contacts'] ],
        [['text' => $phrases['edit_estate_change_link'], 'callback_data' => 'EDIT_ESTATE_CHANGE_Link'] ],
        [['text' => $phrases['edit_estate_change_description'], 'callback_data' => 'EDIT_ESTATE_CHANGE_Description'] ],
    ],
],

// 'show_new_object' => [
//     'inline_keyboard' => [
//         [['text' => "Покажи!", 'callback_data' => 'moderator_show_new_object']], 
//     ],
// ],

// 'skip' => [
//     'inline_keyboard' => [
//         [['text' => "Пропустить", 'callback_data' => 'skip_facility']], 
//     ],
// ]


];