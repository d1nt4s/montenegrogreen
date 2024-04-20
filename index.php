<?php

error_reporting(-1);
ini_set('display_errors', 0);
ini_set('log_errors', 'on');
ini_set('error_log', __DIR__ . '/errors.log');

if (file_exists('errors.log'))
    unlink('errors.log');
if (file_exists('logs.log'))
    unlink('logs.log');

require_once __DIR__ . '/config/config.php';
require_once CORE . '/functions.php';

use \Dantes\Montegreen\Useful\Db;
use \Dantes\Montegreen\MessageHandlerChain;
use \Dantes\Montegreen\Commands\Start;
use \Dantes\Montegreen\Commands\Filtration;
use \Dantes\Montegreen\Commands\AddEstate;
use \Dantes\Montegreen\Commands\ManageEstate;

use \Dantes\Montegreen\StatusHandler;
use \Dantes\Montegreen\Statuses\Estate\Input\InputEstate;
use \Dantes\Montegreen\Statuses\Estate\Edit\EditEstate;

require __DIR__.'/vendor/autoload.php';

try {
    $db_config = require CONFIG . '/db.php';
    $db = (Db::getInstance())->getConnection($db_config);

    if (!$db) {
        throw new Exception("$db нет! БД не подключилась!");
    }

    $telegram = new \Telegram\Bot\Api(TOKEN);
    $update = $telegram->getWebhookUpdate();

    if (!$update) {
        throw new Exception("$update не пришел!");
    }

} catch (Exception $e) {
    error_log($e->getMessage() . PHP_EOL, 3, $GLOBALS['paths']['root'] . '/errors.log');
}

/* РАБОТАЮЩАЯ ЧАСТЬ */

$GLOBALS['restart_bot'] = false;

do {

    // Создаем менеджер обработчик статусов
    if (StatusHandler::isStatusSet($db, $update)) {
        $status_manager = new StatusHandler($db, $update);

        // Создаем обработчики статусов
        $input_estate = new InputEstate($telegram, $db);
        $edit_estate = new EditEstate($telegram, $db);

        $status_manager->addStatusHandler($input_estate);
        $status_manager->addStatusHandler($edit_estate);
        $status_manager->startStatusHandler($update);
        die;
    }


    // Создаем менеджер обработчиков сообщений и коллбэков
    $chain = new MessageHandlerChain();

    // Создаем обработчики
    $start = new Start($telegram);
    $add_estate = new AddEstate($telegram, $db);
    $filtration = new Filtration($telegram, $db);
    $manage_estate = new ManageEstate($telegram, $db);

    $chain->add_handler($start);
    $chain->add_handler($filtration);
    $chain->add_handler($add_estate);
    $chain->add_handler($manage_estate);

    $chain->process_message($update);

} while ($GLOBALS['restart_bot'] == true);
