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

$yandexSmtpHost = 'smtp.yandex.ru';
$yandexEmail = 'user@yandex.ru';
$yandexPassword = 'password';
$yandexSmtpPort = 465;

$targetEmail = $yandexEmail;

$transport = (new Swift_SmtpTransport($yandexSmtpHost, $yandexSmtpPort))
    ->setUsername($yandexEmail)
    ->setPassword($yandexPassword)
    ->setEncryption('SSL');

$mailer = new Swift_Mailer($transport);

$message = (new Swift_Message('Проверка отправки письма'))
    ->setFrom([$yandexEmail => 'Роман Усов'])
    ->setTo([$targetEmail])
    ->setBody('Это письмо отправлено с помощью SwiftMailer');

$result = $mailer->send($message);