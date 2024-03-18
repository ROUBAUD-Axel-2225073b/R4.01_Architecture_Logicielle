<?php

namespace gui;

include_once "View.php";
class ViewOffreEmploi extends View
{
    public function __construct($layout, $login, $presenter)
    {
        parent::__construct($layout, $login);

        $this->title= "Exemple Annonces Basic PHP: Offre d'emploi";

        $this->content = $presenter->getCurrentPostHTML();

        $this->content .= '<a href="/annonces/index.php/annoncesEmploi">Retour</a>';
    }
}