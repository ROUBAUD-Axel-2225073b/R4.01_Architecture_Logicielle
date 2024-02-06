<?php
namespace gui;
include_once "View.php";

class ViewAnnonces extends View
{
    public function __construct($layout, $login, $presenter)
    {
        parent::__construct($layout);

        $this->title= 'Exemple Annonces Basic PHP: Annonces';

        $this->content = "<nav>
            <ul>
                <li><a href='/annonces/index.php/annonces'>Annonces</a></li>
                <li><a href='/annonces/index.php/addpost'>Add Post</a></li>
            </ul>
        </nav>";

        $this->content .= "<p> Hello $login </p>";

        $annoncesHTML = $presenter -> getAllAnnoncesHTML();
        if($annoncesHTML == null){
            $this->content .= 'No posts found.';
        } else {
            $this->content .= $annoncesHTML;
        }
    }
}