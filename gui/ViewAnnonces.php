<?php
namespace gui;

include_once "View.php";

class ViewAnnonces extends View
{
    public function __construct($layout, $login, $presenter)
    {
        parent::__construct($layout, $login);

        $this->title = 'Exemple Annonces Basic PHP: Annonces';
        $this->content = $presenter->getAllAnnonces();

        $this->content .= '<a href="/annonces/index.php/CreateAnnonces">Creation d\'une nouvelle annonce</a>';
    }


}