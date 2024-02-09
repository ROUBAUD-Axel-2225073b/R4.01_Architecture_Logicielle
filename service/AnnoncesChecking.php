<?php

namespace service;

use domaine\Post;
use domaine\User;
use data\DataAccessInterface;

class AnnoncesChecking
{
    protected $annoncesTxt;

    public function getAnnoncesTxt()
    {
        return $this->annoncesTxt;
    }

    public function authenticate($login, $password, DataAccessInterface $data)
    {
        $user = $data->getUser($login, $password);
        return $user instanceof User;
    }

    public function getAllAnnonces(DataAccessInterface $data)
    {
        $annonces = $data->getAllAnnonces();

        $this->annoncesTxt = array();
        foreach ($annonces as $post) {
            if ($post instanceof Post) {
                $this->annoncesTxt[] = [
                    'id' => $post->getId(),
                    'title' => $post->getTitle(),
                    'body' => $post->getBody(),
                    'date' => $post->getDate()
                ];
            }
        }
    }

    public function getPost($id, DataAccessInterface $data)
    {
        $post = $data->getPost($id);
        if ($post instanceof Post) {
            $this->annoncesTxt[] = [
                'id' => $post->getId(),
                'title' => $post->getTitle(),
                'body' => $post->getBody(),
                'date' => $post->getDate()
            ];
        }
    }

    public function addPost($title, $body, DataAccessInterface $data)
    {
        if (strlen($title) > 20 || strlen($body) > 200) {
            echo "Le titre doit contenir au maximum 20 caractères et le contenu au maximum 200 caractères.";
            return false;
        }

        if (!preg_match('/^[a-zA-Z0-9\s]+$/', $title) || !preg_match('/^[a-zA-Z0-9\s]+$/', $body)) {
            echo "Le titre et le contenu ne doivent contenir que du texte.";
            return false;
        }

        return $data->createPost($title, $body, date('Y-m-d'));
    }

    public function getAnnonceById($id)
    {
        foreach ($this->annoncesTxt as $annonce) {
            if ($annonce['id'] == $id) {
                return $annonce;
            }
        }
        return null;
    }

    public function updateAnnonce($id, $title, $content, DataAccessInterface $data)
    {
        if (strlen($title) > 20 || strlen($content) > 200) {
            echo "Le titre doit contenir au maximum 20 caractères et le contenu au maximum 200 caractères.";
            return;
        }

        if (!preg_match('/^[a-zA-Z0-9\s]+$/', $title) || !preg_match('/^[a-zA-Z0-9\s]+$/', $content)) {
            echo "Le titre et le contenu ne doivent contenir que du texte.";
            return;
        }

        $data->updatePost($id, $title, $content, date('Y-m-d'));
    }
}