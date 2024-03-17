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

include_once 'gui/Layout.php';
include_once 'gui/ViewLogin.php';
include_once 'gui/ViewAnnonces.php';
include_once 'gui/ViewPost.php';
include_once 'gui/ViewAnnoncesAlternance.php';
include_once 'gui/ViewCompanyAlternance.php';
include_once 'gui/ViewAnnoncesEmploi.php';
include_once 'gui/ViewOffreEmploi.php';
include_once 'gui/ViewError.php';

use gui\{ViewAnnoncesAlternance,
    ViewAnnoncesEmploi,
    ViewCompanyAlternance,
    ViewLogin,
    ViewAnnonces,
    ViewOffreEmploi,
    ViewPost,
    ViewError,
    Layout};
use control\{Controllers, Presenter};
use data\{AnnonceSqlAccess, ApiAlternance,UserSqlAccess, ApiEmploi};
use service\{AnnoncesChecking, UserChecking};

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
$userCheck = new UserChecking() ;

// intialisation du presenter avec accès aux données de AnnoncesCheking
$presenter = new Presenter($annoncesCheck);

// initialiser la source de données "API Emploi"
$apiEmploi = new ApiEmploi();
$token = $apiEmploi->getToken() ;
echo $token['access_token'];
// chemin de l'URL demandée au navigateur
// (p.ex. /annonces/index.php)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$apiAlternance = new ApiAlternance();
$result = $apiAlternance->getAllAnnonces();
var_dump($result);

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
elseif ( '/annonces/index.php/OffreEmploi' == $uri && isset($_GET['id'])) {
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

elseif ( '/annonces/index.php/annoncesEmploi' == $uri ){
    // affichage de toutes les offres d'emploi

    $controller->annoncesAction($apiEmploi, $annoncesCheck);

    $layout = new Layout("gui/layoutLogged.html" );
    $vueAnnoncesEmploi= new ViewAnnoncesEmploi( $layout,  $_SESSION['login'], $presenter);

    $vueAnnoncesEmploi->display();
}




elseif ( '/annonces/index.php/annonces' == $uri ){
    // affichage de toutes les annonces

    $controller->annoncesAction($dataAnnonces, $annoncesCheck);

    $layout = new Layout("gui/layout.html" );
    $vueAnnonces= new ViewAnnonces( $layout,  $_SESSION['login'], $presenter);

    $vueAnnonces->display();
}
elseif ( '/annonces/index.php/post' == $uri
    && isset($_GET['id'])) {
    // Affichage d'une annonce

    $controller->postAction($_GET['id'], $dataAnnonces, $annoncesCheck);

    $layout = new Layout("gui/layout.html" );
    $vuePost= new ViewPost( $layout,  $_SESSION['login'], $presenter );

    $vuePost->display();
}
elseif ( '/annonces/index.php/error' == $uri ){
    // Affichage d'un message d'erreur

    $layout = new Layout("gui/layout.html" );
    $vueError = new ViewError( $layout, $error, $redirect );

    $vueError->display();
}

else {
    header('Status: 404 Not Found');
    echo '<html><body><h1>My Page NotFound</h1></body></html>';
}

?>
