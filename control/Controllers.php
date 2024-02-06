<?php

namespace control;
use Layout;
use ViewLogin;

include_once "service/AnnoncesChecking.php";

class Controllers
{
    public function loginAction()
    {
        $layout = new Layout("gui/layout.html");
        $vueLogin = new ViewLogin($layout);

        $vueLogin->display();
    }

    public function annoncesAction($login, $password, $data, $annoncesCheck)
    {
        if ($annoncesCheck->authenticate($login, $password, $data))
            $annoncesCheck->getAllAnnonces($data);
    }

    public function postAction($id, $data, $annoncesCheck)
    {
        $annoncesCheck->getPost($id, $data);
    }

    public function addPostAction($title, $body, $data, $annoncesCheck)
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /annonces/index.php');
            exit();
        }

        if ($annoncesCheck->addPost($title, $body, $data)) {
            header('Location: /annonces/index.php/annonces');
            exit();
        }
    }

    public function signupAction($login, $password, $name, $surname, $data)
    {
        if (strlen($password) !== 12) {
            echo "Le mot de passe doit contenir exactement 12 caractères.";
            return;
        }
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12}$/', $password)) {
            echo "Le mot de passe doit contenir des lettres minuscules et majuscules, des chiffres et des caractères spéciaux.";
            return;
        }
        if (!$data->addUser($login, $password, $name, $surname)) {
            return;
        }
        header('Location: /annonces/index.php');
        exit();
    }
}