<?php

namespace control;

use gui\Layout;
use gui\ViewLogin;


include_once "service/AnnoncesChecking.php";

class Controllers
{
    public function loginAction($login, $password, $data, $annoncesCheck): void
    {
        if ($annoncesCheck->authenticate($login, $password, $data)) {
            header('Location: /annonces/index.php/annonces');
            exit();
        } else {
            $layout = new Layout("gui/layout.html");
            $vueLogin = new ViewLogin($layout);
            $vueLogin->display();
        }
    }
    // public function loginAction($login, $password, $data, $annoncesCheck)
    public function annoncesAction($login, $password, $data, $annoncesCheck): bool
    {

        if ($annoncesCheck->authenticate($login, $password, $data)) {
            $annoncesCheck->getAllAnnonces($data);
            return true;
        }
        else return false;

    }

    public function postAction($id, $data, $annoncesCheck)
    {
        $annoncesCheck->getPost($id, $data);
    }


    public function updatePostAction($id, $title, $body, $date, $data)
    {
        $login = $_SESSION['user'];
        if (!$data->isAdmin($login)) {
            echo "You are not authorized to perform this action.";
            return;
        }

        $data->updatePost($id, $title, $body, $date);
        header('Location: /annonces/index.php/annonces');
        exit();
    }

    public function deletePostAction($id, $data)
    {
        $login = $_SESSION['user'];
        if (!$data->isAdmin($login)) {
            echo "You are not authorized to perform this action.";
            return;
        }

        $data->deletePost($id);
        header('Location: /annonces/index.php/annonces');
        exit();
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

    public function signupAction($login, $password, $name, $surname,$admin, $data)
    {
        if (strlen($password) !== 12) {
            echo "Le mot de passe doit contenir exactement 12 caractères.";
            return;
        }
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12}$/', $password)) {
            echo "Le mot de passe doit contenir des lettres minuscules et majuscules, des chiffres et des caractères spéciaux.";
            return;
        }
        if (!$data->addUser($login, $password, $name, $surname, $admin)) {
            return;
        }
        header('Location: /annonces/index.php');
        exit();
    }



    public function changePostAction($id, $data)
    {
        if (!isset($_SESSION['user'])) {
            echo "You must be logged in to perform this action.";
            return;
        }

        $post = $data->getPost($id);
        if ($post != null) {
            $_SESSION['post_to_change'] = $post;
            header('Location: /annonces/index.php/changepost');
            exit();
        }
        return null;
    }

    public function blockUserAction($login, $data)
    {
        $currentLogin = $_SESSION['user'];
        if (!$data->isAdmin($currentLogin)) {
            echo "You are not authorized to perform this action.";
            return;
        }

        if (!$data->blockUser($login)) {
            echo "Failed to block user.";
            return;
        }

        header('Location: /annonces/index.php/annonces');
        exit();
    }



    public function deleteUserAction($login, $data)
    {
        $currentLogin = $_SESSION['user'];
        if (!$data->isAdmin($currentLogin)) {
            echo "You are not authorized to perform this action.";
            return;
        }

        if (!$data->deleteUser($login)) {
            echo "Failed to delete user.";
            return;
        }

        header('Location: /annonces/index.php/annonces');
        exit();
    }
}