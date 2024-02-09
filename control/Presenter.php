<?php

namespace control;

use data\DataAccess;
use service\AnnoncesChecking;

class Presenter
{
    protected $annoncesCheck;
    protected $dataAccess;

    public function __construct(AnnoncesChecking $annoncesCheck, DataAccess $dataAccess)
    {
        $this->annoncesCheck = $annoncesCheck;
        $this->dataAccess = $dataAccess;
    }

    public function getAllAnnoncesHTML()
    {
        $content = null;
        if ($this->annoncesCheck->getAnnoncesTxt() != null) {
            $content = '<h1>List of Posts</h1>  <ul>';
            foreach ($this->annoncesCheck->getAnnoncesTxt() as $post) {
                $content .= ' <li>';
                $content .= '<a href="/annonces/index.php/post?id=' . $post['id'] . '">' . $post['title'] . '</a>';
                $content .= ' </li>';
            }
            $content .= '</ul>';
        }
        return $content;
    }

    public function getCurrentPostHTML($id)
    {
        $post = $this->annoncesCheck->getAnnonceById($id);
        if ($post != null) {
            $content = '<h1>' . $post['title'] . '</h1>';
            $content .= '<p>' . $post['body'] . '</p>';
            return $content;
        }
        return null;
    }
}