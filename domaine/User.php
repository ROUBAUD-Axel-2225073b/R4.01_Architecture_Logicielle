<?php

namespace domaine;

class User
{
    protected $login;
    protected $password;

    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function getLogin()
    {
        return $this->login;
    }
}