<?php

// Telegram
const TOKEN = '6391531275:AAFIMd4tKUYRcAxN0FYpat9ahDso_RPXYz8';
const BASE_URL = 'https://api.telegram.org/bot' . TOKEN . '/';
const BOSS_CHAT_ID = '387634516';

// App

define("ROOT", dirname(__DIR__));
define("CONFIG", ROOT . '/config');
define("CORE", ROOT . '/core');
define("CLASSES", CORE . '/classes');
define("CONTROLLERS", ROOT . '/controllers');
define("MESSAGES", CONTROLLERS . '/messages');
define("CALLBACK", CONTROLLERS . '/callback');
define("DATA", ROOT . '/data');
define("USERS", DATA . '/users');