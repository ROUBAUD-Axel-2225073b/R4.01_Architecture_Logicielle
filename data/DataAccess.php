<?php

namespace data;

use domaine\{Post, User};
use service\DataAccessInterface;

include_once "service/DataAccessInterface.php";

include_once "domaine/Post.php";
include_once "domaine/User.php";


class DataAccess implements DataAccessInterface
{
    protected $dataAccess = null;

    public function __construct($dataAccess)
    {
        $this->dataAccess = $dataAccess;
    }

    public function __destruct()
    {
        $this->dataAccess = null;
    }

    public function getUser($login, $password)
    {
        $user = null;

        $query = 'SELECT login FROM Users WHERE login="' . $login . '" and password="' . $password . '"';
        $result = $this->dataAccess->query($query);

        if ($result->rowCount())
            $user = new User($login, $password);

        $result->closeCursor();

        return $user;
    }

    public function getAllAnnonces()
    {
        $result = $this->dataAccess->query('SELECT * FROM Post');
        $annonces = array();

        while ($row = $result->fetch()) {
            $currentPost = new Post($row['id'], $row['title'], $row['body'], $row['date']);
            $annonces[] = $currentPost;
        }

        $result->closeCursor();

        return $annonces;
    }

    public function getPost($id)
    {
        $id = intval($id);
        $result = $this->dataAccess->query('SELECT * FROM Post WHERE id=' . $id);
        $row = $result->fetch();

        $post = new Post($row['id'], $row['title'], $row['body'], $row['date']);

        $result->closeCursor();

        return $post;
    }

    public function userExists($login)
    {
        $query = 'SELECT login FROM Users WHERE login="' . $login . '"';
        $result = $this->dataAccess->query($query);

        return $result->rowCount() > 0;
    }

    public function addUser($login, $password, $name, $surname)
    {
        if ($this->userExists($login)) {
            echo "Un utilisateur avec cet identifiant existe déjà.";
            return;
        }

        $query = 'INSERT INTO Users (login, password, name, surname) VALUES (?, ?, ?, ?)';
        $stmt = $this->dataAccess->prepare($query);
        $result = $stmt->execute([$login, $password, $name, $surname]);


        if ($result === false) {

            $errorInfo = $stmt->errorInfo();
            echo "Erreur lors de l'insertion : " . $errorInfo[2];
        }
    }

    public function createPost($title, $body,$date)
    {
        $query = 'INSERT INTO Post (date,title,body) VALUES ("' . $date . '","' . $title . '","' . $body . '")';
        $result = $this->dataAccess->query($query);

        $result->closeCursor();
    }

    public function isAdmin ($login) {
        $query = 'SELECT login FROM Users WHERE login="' . $login . '" and admin=1';
        $result = $this->dataAccess->query($query);

        return $result->rowCount() > 0;
    }

    public function updateUser($login, $newPassword, $newName, $newSurname)
    {
        $query = 'UPDATE Users SET password="' . $newPassword . '", name="' . $newName . '", surname="' . $newSurname . '" WHERE login="' . $login . '"';
        $result = $this->dataAccess->query($query);

        $result->closeCursor();
    }

    public function updatePost($id, $title, $body, $date)
    {
        $query = 'UPDATE Post SET title="' . $title . '", body="' . $body . '", date="' . $date . '" WHERE id=' . $id;
        $result = $this->dataAccess->query($query);

        $result->closeCursor();
    }
    public function deletePost($id)
    {
        $query = 'DELETE FROM Post WHERE id=' . $id;
        $result = $this->dataAccess->query($query);

        $result->closeCursor();
    }

    public function deleteUser($login)
    {
        $query = 'DELETE FROM Users WHERE login="' . $login . '"';
        $result = $this->dataAccess->query($query);

        $result->closeCursor();
    }
}

