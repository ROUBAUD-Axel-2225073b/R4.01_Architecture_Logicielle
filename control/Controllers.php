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
        if ($annoncesCheck->authenticate($login, $password, $data)) {
            $_SESSION['user'] = $login;
        }
        if (isset($_SESSION['user'])) {
            $annoncesCheck->getAllAnnonces($data);
        }
    }

    public function postAction($id, $data, $annoncesCheck)
    {
        $annoncesCheck->getPost($id, $data);
    }

    public function createPostAction($title, $body, $date, $data)
    {
        $data->createPost($title, $body, $date);
        header('Location: /annonces/index.php/annonces');
        exit();
    }

    public function updateAnnonceAction($id, $title, $content, $annoncesCheck)
    {
        $annoncesCheck->updateAnnonce($id, $title, $content);
        header('Location: /annonces/index.php/annonces');
        exit();
    }

    public function deleteAnnonceAction($id, $annoncesCheck)
    {
        $annoncesCheck->deleteAnnonce($id);
        header('Location: /annonces/index.php/annonces');
        exit();
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