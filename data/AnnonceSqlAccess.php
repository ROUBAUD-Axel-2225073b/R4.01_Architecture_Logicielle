<?php

namespace data;

use service\DataAccessInterface;
include_once "service/DataAccessInterface.php";

use domain\{User,Post};
include_once "domain/User.php";
include_once "domain/Post.php";

class AnnonceSqlAccess implements DataAccessInterface
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

    public function createAnnonce($login, $info)
    {
        $query = 'INSERT INTO Post(date, title, body, login, location, contactMail, contractType)
            VALUES("' . date('Y-m-d H:i:s') . '","'
            . $info['title'] . '","'
            . $info['body'] . '","'
            . $login . '","'
            . $info['location'] . '","'
            . $info['contactMail'] . '","'
            . $info['contractType'] . '")';

        try {
            $this->dataAccess->query($query);
        }
        catch ( \PDOException $e){
            return false;
        }
        return true;
    }
}
