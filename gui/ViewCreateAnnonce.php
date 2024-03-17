<?php
namespace gui;

include_once "View.php";


class ViewCreateAnnonce extends View
{
    public function __construct($layout)
    {
        parent::__construct($layout);

        $this->title = 'Exemple Annonces Basic PHP: Création d\'une annonce';

        $this->content = ' 
            <form method="post" action="/annonces/index.php/annonces">
                <label for="title"> Titre </label> :
                <input type="text" name="title" id="title" placeholder="defaut" required />
                <br />           
                <label for="contractType"> Type de contrat </label> :
                <select name="contractType" id="contractType">
                    <option value="stage">stage</option>
                    <option value="alternance">alternance</option>
                    <option value="CDD">CDD</option>
                    <option value="CDI">CDI</option>
                </select>
                <br />
                <label for="body"> Description </label> :        
                <textarea name="body" id="body" rows="3" cols="50"> </textarea>
                <br />
                <label for="location"> Localisation </label> :
                <input type="text" name="location" id="location" required />
                <button type="button" id="localizeBtn">ici</button>
                <br />                
                <label for="contactMail"> Mail de contact </label> :
                <input type="text" name="contactMail" id="contactMail" />
                <br />
                <input type="submit" value="Envoyer">
                <input type="reset"> 
            </form>
            <a href="/annonces/index.php/annonces">retour</a>
            <script src="/annonces/gui/js/viewwCreateAnnonceForm.js"></script>
            ';
    }
}