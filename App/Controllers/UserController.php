<?php

namespace App\Controllers;

use App\Models\User;
use App\Base\Config;

class UserController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new User();
    }

    public function actionRegister(array $params)
    {
        $viewData = [];
        $viewData['curSection'] = 'register';

        if (count($params) == 0) {
            // Нет параметров - показываем пустую форму регистрации
            $this->view->render('register', $viewData);
        } else {
            // Есть данные пользователя, пришедшие из браузера (через POST)
            $userData = [];
            foreach (User::$userDataFields as $field) {
                $userData[$field] = isset($params[$field]) ? $params[$field] : '';
            }
            $userData['password'] = trim($userData['password']);
            $userData['password-again'] = trim($userData['password-again']);

            // Проверяем параметры на корректность
            $checkParamsResult = $this->checkRegisterParams($userData['password'], $userData['password-again']);

            if ($checkParamsResult === true) {
                // Входные параметры - OK.  Обращаемся к модели пользователя - регистрируем его
                if ($this->model->isLoginExists($userData['email'])) {
                    // Пользователь с таким логином уже есть
                    header('refresh: 2; url=/user/register');
                    $viewData['errorMessage'] = 'Пользователь с таким логином уже есть';
                    $this->view->render('error', $viewData);
                } else {
                    $userId = $this->model->CreateNewUser($userData);
                    setcookie('user_id', User::encryptUserId($userId, Config::getCookieCryptPassword()), time() + Config::getCookieLiveTime(), '/', $_SERVER['SERVER_NAME']);
                    // После сообщения об успешной регистрации - автоматически перейдём в админ панель через 2 секунды
                    header('refresh: 2; url=/blog/');
                    $viewData['register'] = '<a href="/user/register">Регистрация</a>';
                    $viewData['auth'] = '<a href="/user/auth">Авторизация</a>';
                    $viewData['successMessage'] = "Поздравляем! Регистрация прошла успешно!<br>Ваш логин:&nbsp; <b>{$userData['email']}</b>";
                    $this->view->render('success', $viewData);
                }
            } else {
                // Некорректные входные параметры
                header('refresh: 2; url=/user/register');
                $viewData['errorMessage'] = $checkParamsResult;
                $this->view->render('error', $viewData);
            }
        }
    }


    public function actionAuth(array $params)
    {
        $viewData = [];
        $viewData['curSection'] = 'auth';

        if (count($params) == 0) {
            // Нет параметров - показываем пустую форму авторизации
            $this->view->render('auth', $viewData);
        } else {
            // Есть данные пользователя, пришедшие из браузера (через POST)
            $userData['email'] = isset($params['email']) ? strtolower(trim($params['email'])) : '';
            $userData['password'] = isset($params['password']) ? trim($params['password']) : '';

            if ($this->model->isLoginExists($userData['email'])) {
                // Логин найден - проверяем пароль
                $pwhash = $this->model->getPasswordHash($userData['email']);
                if (password_verify($userData['password'], $pwhash)) {
                    // Успешная авторизация
                    $userId = $this->model->getUserId($userData['email']);
                    setcookie('user_id', User::encryptUserId($userId, Config::getCookieCryptPassword()), time() + Config::getCookieLiveTime(), '/', $_SERVER['SERVER_NAME']);
                    // После приветствия - автоматически перейдём в админ панель через 2 секунды
                    header('refresh: 2; url=/blog/');
                    $userInfo = User::getUserInfoById($userId);
                    $viewData['successMessage'] = "Привет,&nbsp; <b>{$userInfo['name']}</b> !";
                    $this->view->render('success', $viewData);
                } else {
                    $viewData['register'] = '<a href="/user/register">Регистрация</a>';
                    $viewData['auth'] = '<a href="/user/auth">Авторизация</a>';
                    $viewData['errorMessage'] = 'Неверный пароль';
                    $this->view->render('error', $viewData);
                }
            } else {
                $viewData['errorMessage'] = 'Пользователь с таким логином не найден';
                $this->view->render('error', $viewData);
            }
        }
    }

    public function actionLogout()
    {
        setcookie('user_id', '', time() - 5, '/', $_SERVER['SERVER_NAME']);
        header('Location: /');
    }

    private function checkRegisterParams($password, $passwordAgain)
    {
        if ((strlen($password) < Config::getMinPasswordLength()) || (strlen($password) > Config::getMaxPasswordLength())) {
            return 'Пароль должен содержать от ' . Config::getMinPasswordLength() .
                ' до ' . Config::getMaxPasswordLength() . ' символов';
        }
        if ($password != $passwordAgain) {
            return 'Пароли не совпадают';
        }
        return true;
    }
}
