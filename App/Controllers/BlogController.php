<?php

namespace App\Controllers;

use App\Base\Config;
use App\Models\Blog;
use App\Models\User;


class BlogController extends Controller
{
    private $userInfo;
    private $viewData;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Blog();
    }


    private function checkAuth()
    {
        $this->userInfo = self::getUserInfoByCookie();
        if (!$this->userInfo['authorized']) {
            // Надо авторизоваться
            header('Location: /user/auth');
            die;
        }
        // Авторизованный пользователь - получаем его данные
        // для передачи во view
        $this->viewData['userId'] = $this->userInfo['id'];
        $this->viewData['name'] = $this->userInfo['name'];
        $this->viewData['email'] = $this->userInfo['email'];
    }

    public function actionIndex(array $params)
    {
        $this->checkAuth();
        $users = User::getUsersList();
        $posts = $this->model->getPostList();
        $this->viewData['users'] = $users;
        $this->viewData['posts'] = $posts;
        $this->viewData['userId'] = $this->userInfo['id'];
        $cfg = require(ROOT . '/config.php');
        $admin = $cfg['admin'];
        $userAdmin = in_array($this->userInfo['id'],$admin);
        if ($userAdmin) {
            $this->viewData['admin'] = 1;
        }

        $this->view->render('blog', $this->viewData);
    }

    public function actionAdd(array $params)
    {
        if (count($params) == 0) {
            // Нет параметров - показываем пустую форму регистрации
            $viewData = [];
            $viewData['exit'] = '<a href="/user/logout">Выйти</a>';
            $this->userInfo = self::getUserInfoByCookie();
            $viewData['userId'] = $this->userInfo['id'];
            $this->view->render('new_post_blog', $viewData);
        } else {
            // Есть данные пользователя, пришедшие из браузера (через POST)
            $userData = [];

            foreach (Blog::$userDataFields as $field) {
                $userData[$field] = isset($params[$field]) ? $params[$field] : '';
            }
            $this->model->CreateNewPost($userData);
            header('refresh: 2; url=/blog/');
            $viewData['successMessage'] = "Поздравляем! вы добавили новую запись в блог";
            $this->view->render('success', $viewData);

        }
    }

    public function actionDel()
    {
        $this->model->DeletePost($_GET['post_id']);
        header('refresh: 2; url=/blog/');
        $viewData['successMessage'] = "Вы удалили пост id " . $_GET['post_id'];
        $this->view->render('success', $viewData);
    }

    public function actionUserpost()
    {
        $posts = $this->model->getUserPostList($_GET['user_id']);
        $this->viewData['posts'] = $posts;
        $cfg = require(ROOT . '/config.php');
        $admin = $cfg['admin'];
        $userAdmin = in_array($this->userInfo['id'],$admin);
        if ($userAdmin) {
            $this->viewData['admin'] = 1;
        }

        $this->view->render('blog_user', $this->viewData);
    }
}
