<?php
namespace gui;
include_once "View.php";

class ViewSignup extends View
{
    public function __construct($layout)
    {
        parent::__construct($layout);
        $this->title = "Signup";
        $this->content = '
    <form method="post" action="/annonces/index.php/signup">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <label for="name">Nom:</label>
        <input type="text" id="name" name="name" required>
        <label for="surname">Prenom:</label>
        <input type="text" id="surname" name="surname" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <input type="submit" value="Signup">
    </form>
            <a href="/annonces/index.php">Deja un compte</a>';
    }
}