<?php

namespace gui;

use domaine\Post;
use control\Presenter;
use data\DataAccess;
use gui\Layout;

class ViewPost extends View
{
    private $presenter;
    private $dataAccess;

    public function __construct(Layout $layout, Presenter $presenter, DataAccess $dataAccess)
    {
        parent::__construct($layout);
        $this->presenter = $presenter;
        $this->dataAccess = $dataAccess;
    }

    public function render()
    {
        $postId = $_GET['id']; // Get the ID of the post from the URL
        $html = $this->presenter->getCurrentPostHTML($postId);

        // Create the navigation menu
        $menu = '<nav>
            <ul>
                <li><a href="/annonces/index.php/annonces">Annonces</a></li>
                <li><a href="/annonces/index.php/createpost">Add Post</a></li>';
        if (isset($_SESSION['user']) && $this->dataAccess->isAdmin($_SESSION['user'])) {
            $menu .= '<li><a href="/annonces/index.php/changepost?id=' . $postId . '">Change Post</a></li>';
        }
        $menu .= '</ul>
        </nav>';

        // Add the navigation menu to the HTML
        $html = $menu . $html;

        $this->layout->render($html);
    }
}