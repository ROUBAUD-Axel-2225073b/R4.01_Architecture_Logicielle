<?php

namespace service;

class AnnoncesChecking
{
    protected $annoncesTxt;

    public function getAnnoncesTxt()
    {
        return $this->annoncesTxt;
    }

    public function authenticate($login, $password, $data)
    {
        return ($data->getUser($login, $password) != null);
    }

    public function getAllAnnonces($data)
    {
        $annonces = $data->getAllAnnonces();

        $this->annoncesTxt = array();
        foreach ($annonces as $post) {
            $this->annoncesTxt[] = ['id' => $post->getId(), 'title' => $post->getTitle(), 'body' => $post->getBody(), 'date' => $post->getDate()];
        }
    }

    public function getPost($id, $data)
    {
        $post = $data->getPost($id);
        $this->annoncesTxt[] = array('id' => $post->getId(), 'title' => $post->getTitle(), 'body' => $post->getBody(), 'date' => $post->getDate());
    }

    public function addPost($title, $body, $data)
    {
        if (strlen($title) > 20 || strlen($body) > 200) {
            echo "Le titre doit contenir au maximum 20 caractères et le contenu au maximum 200 caractères.";
            return false;
        }

        if (!preg_match('/^[a-zA-Z0-9\s]+$/', $title) || !preg_match('/^[a-zA-Z0-9\s]+$/', $body)) {
            echo "Le titre et le contenu ne doivent contenir que du texte.";
            return false;
        }

        return $data->addPost($title, $body);
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

    public function updateAnnonce($id, $title, $content, $data)
    {
        if (strlen($title) > 20 || strlen($content) > 200) {
            echo "Le titre doit contenir au maximum 20 caractères et le contenu au maximum 200 caractères.";
            return;
        }

        if (!preg_match('/^[a-zA-Z0-9\s]+$/', $title) || !preg_match('/^[a-zA-Z0-9\s]+$/', $content)) {
            echo "Le titre et le contenu ne doivent contenir que du texte.";
            return;
        }

        $data->updatePost($id, $title, $content);
    }
}