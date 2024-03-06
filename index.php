<?php

error_reporting(-1);
ini_set('display_errors', 0);
ini_set('log_errors', 'on');
ini_set('error_log', __DIR__ . '/errors.log');

if (file_exists('errors.log'))
    unlink('errors.log');
if (file_exists('logs.log'))
    unlink('logs.log');

require __DIR__.'/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';
$phrases = require_once CONFIG . '/phrases.php';
$keyboards = require_once CONFIG . '/keyboards.php';
require_once CORE . '/functions.php';

// Import 'controllers'
require_once CONTROLLERS . '/MessageHandlerChain.php';
require_once MESSAGES . '/Start.php';
require_once MESSAGES . '/EstateObject.php';
require_once MESSAGES . '/Filtration.php';

// try {
//     $conn = new PDO("mysql:host=localhost;dbname=montenegrogreen", "jacky", "Re8dfg90745s");
// } catch (PDOException $e) {
//     error_log($e->getMessage() . PHP_EOL, 3, __DIR__ . '/errors.log');
// }

require CLASSES . '/Db.php';

$db_config = require CONFIG . '/db.php';
$db = (Db::getInstance())->getConnection($db_config);

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
$start = new Start($telegram, ['base_keyboard' => $keyboards['base_keyboard']]);
$object = new EstateObject($telegram,[
    'base_keyboard' => $keyboards['base_keyboard'],
    'object_type' =>  $keyboards['object_type'],
    'object_city' => $keyboards['object_city'],
    'object_price' => $keyboards['object_price']],
    $db,
);

$chain->add_handler($start);
$chain->add_handler($object);

$chain->process_message($update);


die;
