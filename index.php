<?php
session_start();

// Include necessary files
include_once 'data/DataAccess.php';
include_once 'control/Controllers.php';
include_once 'control/Presenter.php';
include_once 'service/AnnoncesChecking.php';
include_once 'gui/ViewLogin.php';
include_once 'gui/ViewAnnonces.php';
include_once 'gui/ViewPost.php';
include_once 'gui/Layout.php';
include_once 'gui/ViewSignup.php';
include_once 'gui/ViewCreatePost.php';
include_once 'gui/ViewChangePost.php';

use control\{Controllers, Presenter};
use data\DataAccess;
use gui\{Layout, ViewAnnonces, ViewLogin, ViewPost, ViewSignup, ViewCreatePost, ViewChangePost};
use service\AnnoncesChecking;

// Initialize DataAccess
$data = new DataAccess(new PDO('mysql:host=mysql-roubaud.alwaysdata.net;dbname=roubaud_annonces_db', 'roubaud_annonces', 'vraimdp'));

// Initialize Controllers and Presenter
$controller = new Controllers();
$annoncesCheck = new AnnoncesChecking();
$presenter = new Presenter($annoncesCheck, $data);

// Get the requested URL
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Route the request internally
if ('/annonces/' == $uri || '/annonces/index.php' == $uri) {
    $layout = new Layout("gui/layout.html");
    $vueLogin = new ViewLogin($layout);
    $vueLogin->display();
} elseif ('/annonces/index.php/annonces' == $uri && (isset($_POST['login']) && isset($_POST['password']) || isset($_SESSION['user']))) {
    $login = isset($_POST['login']) ? $_POST['login'] : $_SESSION['user'];
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $controller->annoncesAction($login, $password, $data, $annoncesCheck);
    $layout = new Layout("gui/layout.html");
    $vueAnnonces = new ViewAnnonces($layout, $login, $presenter);
    $vueAnnonces->display();
} elseif ('/annonces/index.php/post' == $uri && isset($_GET['id'])) {
    $controller->postAction($_GET['id'], $data, $annoncesCheck);
    $layout = new Layout("gui/layout.html");
    $vuePost = new ViewPost($layout, $presenter, $data);
    $vuePost->render();
} elseif('/annonces/index.php/createpost' == $uri) {
    if (!isset($_SESSION['user']) || !$data->isAdmin($_SESSION['user'])) {
        header('Location: /annonces/index.php');
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $date = isset($_POST['date']) ? $_POST['date'] : (new \DateTime())->format('Y-m-d');
        $controller->createPostAction($_POST['title'], $_POST['body'], $date, $data);
    }
    $layout = new Layout("gui/layout.html");
    $vueAddPost = new ViewCreatePost($layout);
    $vueAddPost->display();
} elseif('/annonces/index.php/changepost' == $uri && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user']) || !$data->isAdmin($_SESSION['user'])) {
        header('Location: /annonces/index.php');
        exit();
    }
    $redirectUrl = $controller->changePostAction($_POST['id'], $data);
    if ($redirectUrl != null) {
        header('Location: ' . $redirectUrl);
        exit();
    }
    $layout = new Layout("gui/layout.html");
    $vueChangePost = new ViewChangePost($layout, $_SESSION['post_to_change']);
    $vueChangePost->render();
} elseif('/annonces/index.php/signup' == $uri) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->signupAction($_POST['username'], $_POST['password'], $_POST['name'], $_POST['surname'], $data);
    }
    $layout = new Layout("gui/layout.html");
    $vueSignup = new ViewSignup($layout);
    $vueSignup->display();
} else {
    header('Status: 404 Not Found');
    echo '<html><body><h1>Page Not Found</h1></body></html>';
}
?>