<?php
opcache_reset();
define('ROOT', realpath(__DIR__ . '/..'));
define('APP', ROOT . '/App');

//ini_set('display_errors', 1);
//error_reporting(E_ALL);

mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");

function dump ($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}

// Автозагрузка
require_once ROOT . '/vendor/autoload.php';
require_once ROOT . '/config.php';

use App\Base\Config;
use App\Base\Db;
use App\Base\Router;
// Загружаем конфигурацию
Config::loadConfig();

// Установка соединения с БД
Db::getInstance();

// Запускаем Router
Router::run();

