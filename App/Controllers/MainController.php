<?php

namespace App\Controllers;


class MainController extends Controller
{
    public function actionIndex()
    {
        $userInfo = self::getUserInfoByCookie();
        //dump($userInfo);
        $viewData = ['curSection' => ''];
        if ($userInfo['authorized']) {
            $viewData['email'] = $userInfo['email'];
            $viewData['name'] = $userInfo['name'];
            $viewData['exit'] = '<a href="/user/logout">Выйти</a>';
        }
        $this->view->render('main', $viewData);
    }
}
