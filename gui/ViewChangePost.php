<?php
namespace gui;
include_once "View.php";

class ViewChangePost extends View
{
    public function __construct($layout)
    {
        parent::__construct($layout);

        $this->title= 'Exemple Annonces Basic PHP: Create Post';

        $this->content = "<nav>
            <ul>
                <li><a href='/annonces/index.php/annonces'>Annonces</a></li>
                <li><a href='/annonces/index.php/createpost'>Create Post</a></li>
                                <li><a href='/annonces/index.php/changepost'>Change Post</a></li>

            </ul>
        </nav>";

        $this->content .= "<form action='/annonces/index.php/changepost' method='post'>
    <label for='title'>Title:</label><br>
    <input type='text' id='title' name='title'><br>
    <label for='body'>Body:</label><br>
    <textarea id='body' name='body'></textarea><br>
    <input type='submit' value='Submit'>
</form>";
    }
}