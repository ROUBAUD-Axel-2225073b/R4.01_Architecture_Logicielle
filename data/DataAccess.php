<?php

namespace data;

use PDO;
use domaine\{Post, User};
use service\DataAccessInterface;
include_once 'service/DataAccessInterface.php';
include_once 'domaine/Post.php';
include_once 'domaine/User.php';

class DataAccess implements DataAccessInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getUser($login, $password)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM Users WHERE login = :login AND password = :password');
        $stmt->execute(['login' => $login, 'password' => $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ? new User($user['login'], $user['password']) : null;
    }

    public function getAllAnnonces()
    {
        $stmt = $this->pdo->query('SELECT * FROM Post ORDER BY date DESC');
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function($post) {
            return new Post($post['id'], $post['title'], $post['body'], $post['date']);
        }, $posts);
    }

    public function getPost($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM Post WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        return $post ? new Post($post['id'], $post['title'], $post['body'], $post['date']) : null;
    }

    public function addUser($login, $password, $name, $surname)
    {
        $stmt = $this->pdo->prepare('INSERT INTO Users (login, password, name, surname) VALUES (:login, :password, :name, :surname)');
        $stmt->execute(['login' => $login, 'password' => $password, 'name' => $name, 'surname' => $surname]);
    }

    public function userExists($login)
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM Users WHERE login = :login');
        $stmt->execute(['login' => $login]);
        return $stmt->fetchColumn() > 0;
    }

    public function createPost($title, $body, $date)
    {
        $stmt = $this->pdo->prepare('INSERT INTO Post (title, body, date) VALUES (:title, :body, :date)');
        $stmt->execute(['title' => $title, 'body' => $body, 'date' => $date]);
    }

    public function isAdmin($login)
    {
        $stmt = $this->pdo->prepare('SELECT admin FROM Users WHERE login = :login');
        $stmt->execute(['login' => $login]);
        return $stmt->fetchColumn() == 1;
    }

    public function deleteUser($login)
    {
        $stmt = $this->pdo->prepare('DELETE FROM Users WHERE login = :login');
        $stmt->execute(['login' => $login]);
    }

    public function updatePost($id, $title, $body, $date)
    {
        $stmt = $this->pdo->prepare('UPDATE Post SET title = :title, body = :body, date = :date WHERE id = :id');
        $stmt->execute(['id' => $id, 'title' => $title, 'body' => $body, 'date' => $date]);
    }
}