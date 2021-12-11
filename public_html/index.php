<?php
opcache_reset();
define('ROOT', realpath(__DIR__ . '/..'));
define('APP', ROOT . '/App');

ini_set('display_errors', 1);
error_reporting(E_ALL);

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
use Intervention\Image\ImageManagerStatic as Image;

$watermark =  ROOT . '/public_html/img/logo.png';
$picturePath = ROOT . '/public_html/img/img.png';
$imageNewPath = ROOT . '/public_html/img/img.webp';
$ratio = function($img) {
    $img->aspectRatio();
    $img->upsize();
};
$image = Image::make($picturePath)->insert($watermark, 'top-left')->resize(500,null,$ratio)->encode('webp')->save($imageNewPath);

// Загружаем конфигурацию
Config::loadConfig();

// Установка соединения с БД
Db::getInstance();

// Запускаем Router
Router::run();

