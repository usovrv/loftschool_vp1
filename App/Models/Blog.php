<?php

namespace App\Models;
use App\Base\Db;

class Blog
{
    public static $userDataFields = ['userId', 'name', 'text'];

    public function CreateNewPost($userData)
    {
        $db = Db::getInstance();
        $query = "INSERT INTO post(`userid`, `name`, `message`) VALUES (:userid, :name, :message)";
        $result = $db->exec($query, __METHOD__, [
            ':userid' => $userData['userId'],
            ':name' => htmlspecialchars($userData['name']),
            ':message' => htmlspecialchars($userData['text']),
        ]);
        if (!$result) {
            return false;
        }

        return $db->lastInsertId();
    }

    public function getPostList()
    {
        $db = Db::getInstance();
        $query = "SELECT * FROM post";
        $data = $db->fetchAll($query, __METHOD__, []);
        $posts = [];
        foreach ($data as $post) {
            $posts[] = $post;
        }

        return $posts;
    }

    public function getUserPostList($userId)
    {
        $db = Db::getInstance();
        $query = "SELECT * FROM post WHERE userid = :userid";
        $data = $db->fetchAll($query, __METHOD__, [':userid' => $userId]);
        $posts = [];
        foreach ($data as $post) {
            $posts[] = $post;
        }

        return $posts;
    }

    public function DeletePost($postId)
    {
        $db = Db::getInstance();
        $query = "DELETE FROM post WHERE id = :id";
        $del = $db->fetchOne($query, __METHOD__, [':id' => $postId]);
        return $del;
    }
}
