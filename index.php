<?php
session_start();

// charge et initialise les bibliothèques globales
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

use control\{Controllers, Presenter};
use data\DataAccess;
use gui\{Layout, ViewAnnonces, ViewLogin, ViewPost, ViewSignup, ViewCreatePost};
use service\AnnoncesChecking;

$data = null;
try {
    // construction du modèle
    $data = new DataAccess( new PDO('mysql:host=mysql-roubaud.alwaysdata.net;dbname=roubaud_annonces_db', 'roubaud_annonces', 'vraimdp') );


} catch (PDOException $e) {
    print "Erreur de connexion !: " . $e->getMessage() . "<br/>";
    die();
}

// initialisation du controller
$controller = new Controllers();

// intialisation du cas d'utilisation AnnoncesChecking
$annoncesCheck = new AnnoncesChecking() ;

// intialisation du presenter avec accès aux données de AnnoncesCheking
$presenter = new Presenter($annoncesCheck);

// chemin de l'URL demandée au navigateur
// (p.ex. /annonces/index.php)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// route la requête en interne
// i.e. lance le bon contrôleur en focntion de la requête effectuée
if ( '/annonces/' == $uri || '/annonces/index.php' == $uri) {

    $layout = new Layout("gui/layout.html" );
    $vueLogin = new ViewLogin( $layout );

    $vueLogin->display();
}
elseif ( '/annonces/index.php/annonces' == $uri
    && (isset($_POST['login']) && isset($_POST['password']) || isset($_SESSION['user']))) {
    $login = isset($_POST['login']) ? $_POST['login'] : $_SESSION['user'];
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    $controller->annoncesAction($login, $password, $data, $annoncesCheck);

    $layout = new Layout("gui/layout.html" );
    $vueAnnonces= new ViewAnnonces( $layout, $login, $presenter);

    $vueAnnonces->display();
}


elseif ( '/annonces/index.php/post' == $uri
    && isset($_GET['id'])) {

    $controller->postAction($_GET['id'], $data, $annoncesCheck);

    $layout = new Layout("gui/layout.html" );
    $vuePost= new ViewPost( $layout, $presenter );

    $vuePost->display();
}

elseif('/annonces/index.php/createpost' == $uri) {
    if (!isset($_SESSION['user'])) {
        header('Location: /annonces/index.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $date = isset($_POST['date']) ? $_POST['date'] : (new \DateTime())->format('Y-m-d');
        $controller->createPostAction($_POST['title'], $_POST['body'], $date, $data);
    }

    $layout = new Layout("gui/layout.html" );
    $vueAddPost= new ViewCreatePost($layout);
    $vueAddPost->display();
}

elseif('/annonces/index.php/signup' == $uri) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->signupAction($_POST['username'], $_POST['password'], $_POST['name'], $_POST['surname'], $data);
    }

    $layout = new Layout("gui/layout.html" );
    $vueSignup= new ViewSignup($layout);
    $vueSignup->display();
}
else {
    header('Status: 404 Not Found');
    echo '<html><body><h1>My Page NotFound</h1></body></html>';
}

?>