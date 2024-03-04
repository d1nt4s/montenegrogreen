<?php

error_reporting(-1);
ini_set('display_errors', 0);
ini_set('log_errors', 'on');
ini_set('error_log', __DIR__ . '/errors.log');

if (file_exists('errors.log'))
    unlink('errors.log');
if (file_exists('logs.txt'))
    unlink('logs.txt');

require __DIR__.'/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';
$phrases = require_once CONFIG . '/phrases.php';
$keyboards = require_once CONFIG . '/keyboards.php';
require_once 'functions.php';

// Import 'controllers'
require_once CONTROLLERS . '/MessageHandlerChain.php';
require_once MESSAGES . '/Start.php';
require_once MESSAGES . '/EstateObject.php';
require_once MESSAGES . '/Filtration.php';

try {
    $conn = new PDO("mysql:host=localhost;dbname=montenegrogreen", "jacky", "Re8dfg90745s");
} catch (PDOException $e) {
    error_log($e->getMessage() . PHP_EOL, 3, __DIR__ . '/errors.log');
}


// file_put_contents(ROOT . '/logs.txt', CONFIG . PHP_EOL, FILE_APPEND);

$telegram = new \Telegram\Bot\Api(TOKEN);
$update = $telegram->getWebhookUpdate();

if (!$update) {
    die;
}

// $text = $update['message']['text'] ?? '';

// Creating handler manager
$chain = new MessageHandlerChain();

// Creating handlers
$start = new Start($telegram, [$keyboards['base_keyboard']]);
$object = new EstateObject($telegram, [$keyboards['base_keyboard'], $keyboards['object_type'], $keyboards['object_city'], $keyboards['object_price']]);

$chain->add_handler($start);

$chain->process_message($update);


die;
