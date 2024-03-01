<?php

error_reporting(-1);
ini_set('display_errors', 0);
ini_set('log_errors', 'on');
ini_set('error_log', __DIR__ . '/errors.log');

if (file_exists('errors.log'))
    unlink('errors.log');
if (file_exists('logs.txt'))
    unlink('logs.txt');

/**
 * @code Telegram\Bot\Api
 */

require __DIR__.'/vendor/autoload.php';
require_once '/config/config.php';
$phrases = require_once '/config/phrases.php';
require_once 'functions.php';
require_once '/config/variables.php';

try {
    $conn = new PDO("mysql:host=localhost;dbname=montenegrogreen", "jacky", "Re8dfg90745s");
} catch (PDOException $e) {
    error_log($e->getMessage() . PHP_EOL, 3, __DIR__ . '/errors.log');
}

$telegram = new \Telegram\Bot\Api(TOKEN);
$update = $telegram->getWebhookUpdate();

if (isset($update['message']['chat']['id'])) {
    $chat_id = $update['message']['chat']['id'];
} elseif (isset($update['callback_query']['message']['chat']['id'])) {
    $chat_id = $update['callback_query']['message']['chat']['id'];
}
$text = $update['message']['text'] ?? '';

if (!$chat_id) {
    die;
}

if ($text == '/start' || $text == $phrases['setup']) {

    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text'=> $hello,
        'reply_markup' => json_encode($keyboard),
    ]);

} else if ($text == '/showall' || $text == $phrases['showall']) {

    $query = "SELECT * FROM objects";
    $stmt = show($query, $telegram, $chat_id, $conn);

} else if (str_contains($text,'/filtrate') || $text === $phrases['filter']) {

    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text'=> 'Выберите тип обьекта',
        'reply_markup' => new Telegram\Bot\Keyboard\Keyboard($house_filter),
    ]);
} elseif (isset($update['callback_query'])) {

    if (str_contains($update['callback_query']['data'], 'type')) {

        $telegram->answerCallbackQuery([
            'show_alert' => true,
            'callback_query_id' => $update['callback_query']['id'],
        ]);
        $telegram->editMessageText([
            'chat_id' => $chat_id,
            'message_id' => $update['callback_query']['message']['message_id'],
            'text' => "Выберите города обьекта и нажмите 'Следующий раздел'",
            'parse_mode' => 'HTML',
            'reply_markup' => new Telegram\Bot\Keyboard\Keyboard($city_filter_1),
        ]);

        $fd = fopen("filter.txt", 'w') or ($telegram->sendMessage(['chat_id' => $chat_id,'text'=> "Filter.txt can't be created!"]) and die());
        fwrite($fd, 'type:' . $phrases[$update['callback_query']['data']] . PHP_EOL);
        fclose($fd);

    } else if (str_contains($update['callback_query']['data'], 'city')) { 
   
        if (str_contains($update['callback_query']['data'], 'page')) { 

            if (str_contains($update['callback_query']['data'], 'page_1')) {
                $telegram->answerCallbackQuery([
                    'callback_query_id' => $update['callback_query']['id'],
                    'show_alert' => true,
                ]);
                $telegram->editMessageText([
                    'chat_id' => $chat_id,
                    'message_id' => $update['callback_query']['message']['message_id'],
                    'text' => "Выберите города обьекта и нажмите 'Следующий раздел'",
                    'parse_mode' => 'HTML',
                    'reply_markup' => new Telegram\Bot\Keyboard\Keyboard($city_filter_1),
                ]);
            } else if (str_contains($update['callback_query']['data'], 'page_2')) {
                $telegram->answerCallbackQuery([
                    'callback_query_id' => $update['callback_query']['id'],
                    'show_alert' => true,
                ]);
                $telegram->editMessageText([
                    'chat_id' => $chat_id,
                    'message_id' => $update['callback_query']['message']['message_id'],
                    'text' => "Выберите города обьекта и нажмите 'Следующий раздел'",
                    'parse_mode' => 'HTML',
                    'reply_markup' => new Telegram\Bot\Keyboard\Keyboard($city_filter_2),
                ]);
            }

        } else if (str_contains($update['callback_query']['data'], 'stage')) { 

            $file = file('filter.txt'); 
            $last_line = $file[count($file) - 1];
            if (!str_contains($last_line, 'city')) {
                $telegram->answerCallbackQuery([
                    'callback_query_id' => $update['callback_query']['id'],
                    'show_alert' => true,
                ]);
                $telegram->editMessageText([
                    'chat_id' => $chat_id,
                    'message_id' => $update['callback_query']['message']['message_id'],
                    'text' => "Сначала выберите город",
                    'parse_mode' => 'HTML',
                    'reply_markup' => new Telegram\Bot\Keyboard\Keyboard($city_filter_1),
                ]); 
                die;
            } else {
                $telegram->answerCallbackQuery([
                    'callback_query_id' => $update['callback_query']['id'],
                    'show_alert' => true,
                ]);
                $telegram->editMessageText([
                    'chat_id' => $chat_id,
                    'message_id' => $update['callback_query']['message']['message_id'],
                    'text' => "Выбери ценовой диапазон",
                    'parse_mode' => 'HTML',
                    'reply_markup' => new Telegram\Bot\Keyboard\Keyboard($price_filter),
                ]);
            }

        } else {

            $telegram->answerCallbackQuery([
                'callback_query_id' => $update['callback_query']['id'],
                'show_alert' => true,
            ]);

            $telegram->editMessageText([
                'chat_id' => $chat_id,
                'message_id' => $update['callback_query']['message']['message_id'],
                'text' => "Город {$phrases[$update['callback_query']['data']]} выбран! Выберите еще или нажмите 'Следующий раздел'",
                'parse_mode' => 'HTML',
                'reply_markup' => new Telegram\Bot\Keyboard\Keyboard($city_filter_1),
            ]);

            $fd = fopen("filter.txt", 'a+') or ($telegram->sendMessage(['chat_id' => $chat_id,'text'=> "Filter.txt can't be created!"]) and die());
            fwrite($fd, 'city:' . $phrases[$update['callback_query']['data']] . PHP_EOL);
            fclose($fd);

        }
    
    
    } else if (str_contains($update['callback_query']['data'], 'price')) {

        if ($update['callback_query']['data'] == 'price_value_your') {

            $telegram->answerCallbackQuery([
                'callback_query_id' => $update['callback_query']['id'],
                'show_alert' => true,
            ]);

            $telegram->editMessageText([
                'chat_id' => $chat_id,
                'message_id' => $update['callback_query']['message']['message_id'],
                'text' => "Напишите диапазон в формате от-до" . PHP_EOL . "Например: 75000-395000",
                'parse_mode' => 'HTML',
            ]);

        } else {

            $telegram->answerCallbackQuery([
                'callback_query_id' => $update['callback_query']['id'],
                'show_alert' => true,
            ]);

            $telegram->editMessageText([
                'chat_id' => $chat_id,
                'message_id' => $update['callback_query']['message']['message_id'],
                'text' => "Обьекты подходящие по фильтру:",
                'parse_mode' => 'HTML',
            ]);

            switch ($update['callback_query']['data']) {
                case 'price_value_1':
                    $from = '0';
                    $to = '100000';
                    break;
                case 'price_value_2':
                    $from = '100000';
                    $to = '300000';
                    break;
                case 'price_value_3':
                    $from = '300000';
                    $to = '500000';
                    break;
                case 'price_value_4':
                    $from = '500000';
                    $to = '50000000';
                    break;
                case 'price_value_5':
                    $from = '0';
                    $to = '50000000';
                    break;
            }

            $fd = fopen("filter.txt", 'a+') or ($telegram->sendMessage(['chat_id' => $chat_id,'text'=> "Filter.txt can't be created!"]) and die());
            fwrite($fd, "selling_price > {$from} AND selling_price < {$to}");
            fclose($fd);

            $query = create_filtration_query($telegram, $chat_id);
            debug($query);
            $stmt = show($query, $telegram, $chat_id, $conn);

            if ($stmt->rowCount() == 0) {
                $telegram->editMessageText([
                    'chat_id' => $chat_id,
                    'message_id' => $update['callback_query']['message']['message_id'],
                    'text' => "К сожалению подходящих обьектов не нашлось!",
                    'parse_mode' => 'HTML',
                ]);
            }

        }

    }



} else if (str_contains($text,'-')) {

    $price_range = explode("-", $text);
    if (is_numeric($price_range[0]) && is_numeric($price_range[1])) {

        $fd = fopen("filter.txt", 'a+') or ($telegram->sendMessage(['chat_id' => $chat_id,'text'=> "Filter.txt can't be created!"]) and die());
        fwrite($fd, "selling_price > {$price_range[0]} AND selling_price < {$price_range[1]}");
        fclose($fd);
    
        $telegram->sendMessage([
            'chat_id' => $chat_id,
            'text'=> 'Диапазон установлен!',
        ]);

        $query = create_filtration_query($telegram, $chat_id);
        debug($query);
        $stmt = show($query, $telegram, $chat_id, $conn);

        if ($stmt->rowCount() == 0) {
            $telegram->sendMessage([
                'chat_id' => $chat_id,
                'text'=> 'К сожалению подходящих обьектов не нашлось!',
            ]);
        }

    }

} else if ($text == '/list' || $text == $phrases['list']) {

    try {
        $query = "SELECT * FROM objects";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        if($stmt->rowCount() > 0)
        {
            $list = 'Перечень всех обьектов: ' . PHP_EOL;
            foreach ($stmt as $row) {
                $list .= "/show{$row['id']} | {$row['type']} | {$row['city']} | {$row['selling_price']} " . PHP_EOL;
            }
        }

        $telegram->sendMessage([
            'chat_id' => $chat_id,
            'text'=> $list,
        ]);

    } catch (\Telegram\Bot\Exceptions\TelegramSDKException $e) {
        error_log($e->getMessage() . PHP_EOL, 3, __DIR__ . '/errors.log');
    }
    
} else if (str_contains($text,'/show')) {

    $id = trim(substr($text, 5));

    if (!is_numeric($id)) {
        $telegram->sendMessage([
            'chat_id' => $chat_id,
            'text'=> "Введите номер обьекта после /show",
        ]); 
        die;
    }

    $query = "SELECT * FROM objects WHERE id={$id}";
    $stmt = show($query, $telegram, $chat_id, $conn);

} else {
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text'=> 'Введи команду или выбери ее посредством клавиатуры!',
    ]);
}

die;
