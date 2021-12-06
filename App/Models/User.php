<?php

namespace App\Models;
use App\Base\Db;

class User
{
    public static $userDataFields = ['name', 'email', 'password', 'password-again'];

    public function CreateNewUser($userData)
    {
        $db = Db::getInstance();
        $query = "INSERT INTO users(email, `name`, `password_hash`) VALUES (:email, :name, :password_hash)";
        $result = $db->exec($query, __METHOD__, [
            ':email' => htmlspecialchars($userData['email']),
            ':name' => htmlspecialchars($userData['name']),
            ':password_hash' => password_hash($userData['password'], PASSWORD_BCRYPT)
        ]);
        if (!$result) {
            return false;
        }

        return $db->lastInsertId();
    }

    public function isLoginExists($email)
    {
        $db = Db::getInstance();
        $query = "SELECT * FROM users WHERE email = :email";
        return $db->fetchOne($query, __METHOD__, [':email' => $email]);
    }

    public function getPasswordHash($email)
    {
        $db = Db::getInstance();
        $query = "SELECT `password_hash` FROM users WHERE email = :email";
        $password = $db->fetchOne($query, __METHOD__, [':email' => $email]);
        return $password['password_hash'];
    }

    public function getUserId($email)
    {
        $db = Db::getInstance();
        $query = "SELECT `id` FROM users WHERE email = :email";
        $id = $db->fetchOne($query, __METHOD__, [':email' => $email]);
        return $id['id'];
    }

    public function getUsersList()
    {
        $db = Db::getInstance();
        $query = "SELECT * FROM users";
        $data = $db->fetchAll($query, __METHOD__, []);
        $users = [];
        foreach ($data as $elem) {
            $users[] = $elem;
        }

        return $users;
    }

    public static function getUserInfoById($id)
    {
        $db = Db::getInstance();
        $query = "SELECT * FROM users WHERE id = :id";
        $userInfo = $db->fetchOne($query, __METHOD__, [':id' => $id]);
        //dump($user);
        return $userInfo;
    }

    public static function encryptUserId($id, $password)
    {
        return openssl_encrypt($id, 'AES-128-ECB', $password);
    }


    public static function decryptUserId($cryptedId, $password)
    {
        return openssl_decrypt($cryptedId, 'AES-128-ECB', $password);
    }

}
