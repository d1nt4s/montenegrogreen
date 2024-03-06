<?php

return [
    'host' => 'localhost',
    'dbname' => 'montenegrogreen', //montenegrogreen
    'username' => 'jacky', //jacky
    'password' => 'Re8dfg90745s', //Re8dfg90745s
    'charset' => 'utf8', // utf8mb4 если кодировка смайликов поломается
    'options' => [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ],
];