<?php
namespace gui;
include_once "View.php";

class ViewCreatePost extends View
{
    public function __construct($layout)
    {
        parent::__construct($layout);

        $this->title= 'Exemple Annonces Basic PHP: Add Post';

        $this->content = "<nav>
            <ul>
                <li><a href='/annonces/index.php/annonces'>Annonces</a></li>
                <li><a href='/annonces/index.php/createpost'>Add Post</a></li>
            </ul>
        </nav>";

        $this->content .= "<form action='/annonces/index.php/createpost' method='post'>
    <label for='title'>Title:</label><br>
    <input type='text' id='title' name='title'><br>
    <label for='body'>Body:</label><br>
    <textarea id='body' name='body'></textarea><br>
    <label for='date'>Date:</label><br>
    <input type='date' id='date' name='date'><br>
    <input type='submit' value='Submit'>
</form>";
    }
}