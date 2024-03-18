<?php

// charge et initialise les bibliothèques globales
include_once 'data/AnnonceSqlAccess.php';
include_once 'data/UserSqlAccess.php';
include_once 'data/ApiAlternance.php';
include_once 'data/ApiEmploi.php';

include_once 'control/Controllers.php';
include_once 'control/Presenter.php';

include_once 'service/AnnoncesChecking.php';
include_once 'service/UserChecking.php';
include_once 'service/AnnonceCreation.php';

include_once 'gui/Layout.php';
include_once 'gui/ViewLogin.php';
include_once 'gui/ViewAnnonces.php';
include_once 'gui/ViewPost.php';
include_once 'gui/ViewError.php';
include_once 'gui/ViewCompanyAlternance.php';
include_once 'gui/ViewAnnoncesAlternance.php';
include_once 'gui/ViewAnnoncesEmploi.php';
include_once 'gui/ViewOffreEmploi.php';
include_once 'gui/ViewCreateAnnonce.php';

use gui\{ViewAnnoncesAlternance,
    ViewAnnoncesEmploi,
    ViewCompanyAlternance,
    ViewCreateAnnonce,
    ViewLogin,
    ViewAnnonces,
    ViewOffreEmploi,
    ViewPost,
    ViewError,
    Layout};
use control\{Controllers, Presenter};
use data\{AnnonceSqlAccess, ApiAlternance, ApiEmploi, UserSqlAccess};
use service\{AnnonceCreation, AnnoncesChecking, UserChecking};

$data = null;
try {
    $bd = new PDO('mysql:host=mysql-roubaud.alwaysdata.net;dbname=roubaud_annonces_db', 'roubaud_annonces', 'vraimdp');
    // construction du modèle
    $dataAnnonces = new AnnonceSqlAccess($bd);
    $dataUsers = new UserSqlAccess($bd);

} catch (PDOException $e) {
    print "Erreur de connexion !: " . $e->getMessage() . "<br/>";
    die();
}

// initialisation du controller
$controller = new Controllers();

// intialisation du cas d'utilisation service\AnnoncesChecking
$annoncesCheck = new AnnoncesChecking() ;

// intialisation du cas d'utilisation service\UserChecking
$userCheck = new UserChecking();

// initialisation du cas d'utilisation service\AnnonceCreation
$annonceCreation = new AnnonceCreation();

// intialisation du presenter avec accès aux données de AnnoncesCheking
$presenter = new Presenter($annoncesCheck);

// initialisation de l'API alternance
$apiAlternance = new ApiAlternance();

// initialiser la source de données "API Emploi"
$apiEmploi = new ApiEmploi();
$token = $apiEmploi->getToken();

// chemin de l'URL demandée au navigateur
// (p.ex. /annonces/index.php)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// définition d'une session d'une heure
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);
session_start();

// Authentification et création du compte (sauf pour le formulaire de connexion et la route de déconnexion)
if ( '/annonces/' != $uri and '/annonces/index.php' != $uri and '/annonces/index.php/logout' != $uri ){

    $error = $controller->authenticateAction($userCheck, $dataUsers);

    if( $error != null )
    {
        $uri='/annonces/index.php/error' ;
        if( $error == 'bad login or pwd' or $error == 'not connected')
            $redirect = '/annonces/index.php';
    }
}

// route la requête en interne
// i.e. lance le bon contrôleur en fonction de la requête effectuée
if ( '/annonces/' == $uri || '/annonces/index.php' == $uri || '/annonces/index.php/logout' == $uri) {
    // affichage de la page de connexion

    session_destroy();
    $layout = new Layout("gui/layout.html" );
    $vueLogin = new ViewLogin( $layout );

    $vueLogin->display();
}
elseif ( '/annonces/index.php/annonces' == $uri ){
    if (isset($_POST['contractType'])) {
        // création d'une annonce
        $controller->annonceCreationAction($_SESSION['login'], $_POST, $dataAnnonces, $annonceCreation);
    }

    // affichage de toutes les annonces

    $controller->annoncesAction($dataAnnonces, $annoncesCheck);

    $layout = new Layout("gui/layoutLogged.html" );
    $vueAnnonces= new ViewAnnonces( $layout,  $_SESSION['login'], $presenter);

    $vueAnnonces->display();
}
elseif ( '/annonces/index.php/post' == $uri
    && isset($_GET['id'])) {
    // Affichage d'une annonce

    $controller->postAction($_GET['id'], $dataAnnonces, $annoncesCheck);

    $layout = new Layout("gui/layoutLogged.html" );
    $vuePost= new ViewPost( $layout, $presenter,  $_SESSION['login'] );

    $vuePost->display();
}
elseif ( '/annonces/index.php/createAnnonce' == $uri ){
    // affichage du formulaire de création d'une annonce

    $layout = new Layout("gui/layoutLogged.html" );
    $vueCreateAnnonce = new ViewCreateAnnonce( $layout );

    $vueCreateAnnonce->display();
}
elseif ( '/annonces/index.php/error' == $uri ){
    // Affichage d'un message d'erreur

    $layout = new Layout("gui/layout.html" );
    $vueError = new ViewError( $layout, $error, $redirect );

    $vueError->display();
}
elseif ( '/annonces/index.php/annoncesEmploi' == $uri ){
    // affichage de toutes les offres d'emploi

    $controller->annoncesAction($apiEmploi, $annoncesCheck);

    $layout = new Layout("gui/layoutLogged.html" );
    $vueAnnoncesEmploi= new ViewAnnoncesEmploi( $layout,  $_SESSION['login'], $presenter);

    $vueAnnoncesEmploi->display();
}
elseif ( '/annonces/index.php/offreEmploi' == $uri && isset($_GET['id'])) {
    // affichage de toutes les offres d'emploi

    $controller->postAction($_GET['id'], $apiEmploi, $annoncesCheck);

    $layout = new Layout("gui/layoutLogged.html" );
    $vuePostEmploi= new ViewOffreEmploi( $layout,  $_SESSION['login'], $presenter);

    $vuePostEmploi->display();
}
elseif ( '/annonces/index.php/annoncesAlternance' == $uri ){
    // Affichage de toutes les entreprises offrant de l'alternance

    $controller->annoncesAction($apiAlternance, $annoncesCheck);

    $layout = new Layout("gui/layoutLogged.html" );
    $vueAnnoncesAlternance= new ViewAnnoncesAlternance( $layout,  $_SESSION['login'], $presenter);

    $vueAnnoncesAlternance->display();
}

elseif ( '/annonces/index.php/companyAlternance' == $uri
    && isset($_GET['id'])) {
    // Affichage d'une entreprise offrant de l'alternance

    $controller->postAction($_GET['id'], $apiAlternance, $annoncesCheck);

    $layout = new Layout("gui/layoutLogged.html" );
    $vuePostAlternance = new ViewCompanyAlternance( $layout,  $_SESSION['login'], $presenter );

    $vuePostAlternance->display();
}
else {
    header('Status: 404 Not Found');
    echo '<html><body><h1>My Page NotFound</h1></body></html>';
}

?>