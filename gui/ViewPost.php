<?php

namespace gui;

use control\Presenter;

class ViewPost
{
    private $layout;
    private $presenter;

    public function __construct(Layout $layout, Presenter $presenter)
    {
        $this->layout = $layout;
        $this->presenter = $presenter;
    }

    public function render()
    {
        $postId = $_GET['id']; // Get the ID of the post from the URL
        $html = $this->presenter->getCurrentPostHTML($postId);
        $this->layout->render($html);
    }
}