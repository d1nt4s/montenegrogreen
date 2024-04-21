<?php

function get_keyboard($keyboard_file_name)
{
    try {
        $phrases = include 'phrases.php';
        if (!is_array($phrases)) {
            throw new Exception('Массив $phrases отсутствует!');
        }

        $keyboards_folder = $GLOBALS['paths']['keyboards'];
        $keyboard = require $keyboards_folder . '/' . $keyboard_file_name . '.php';
        return $keyboard;
    } catch (Exception $e) {
        error_log($e->getMessage() . PHP_EOL, 3, $GLOBALS['paths']['root'] . '/errors.log');
    }
}