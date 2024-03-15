<?php

namespace data;

use service\AnnonceAccessInterface;
include_once "service/AnnonceAccessInterface.php";
class ApiAlternance implements AnnonceAccessInterface
{

    public function getAllAnnonces(){

        $romes = urlencode('M1801,M1802,M1803,M1804,M1805,M1806,M1810');

        $latitudeAix = '43.529742';
        $longitudeAix = '5.447427';
        $radius = '100';
        $inseeAix = '13100';

        // URL de l'API
        $apiUrl = "https://labonnealternance-recette.apprentissage.beta.gouv.fr/api/V1/jobs";

        // paramètres de la requête HTTP
        $query ='?romes='.$romes.'&latitude='.$latitudeAix.'&longitude='.$longitudeAix.'&radius='.$radius.'&insee='.$inseeAix.'&sources=&caller=contact%40domaine%20nom_de_societe&opco=AKTO%7CAFDAS%7CATLAS%7CCONSTRUCTYS%7COPCOMMERCE%7COCAPIAT%7COPCO2I%7CEP%7CMOBILITE%7CSANTE%7CUNIFORMATION';

        // initialisation de la connexion à l'API avec CURL
        $curlConnection  = curl_init();

        // définition des paramètres de la requête CURL
        $params = array(
            CURLOPT_URL =>  $apiUrl.$query,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('accept: application/json')
        );
        curl_setopt_array($curlConnection, $params);

        // exécution de la requête HTTP avec CURL
        $response = curl_exec($curlConnection);
        curl_close($curlConnection);

        if( !$response )
            echo curl_error($curlConnection);

        // transformation du JSON récupéré en tableau associatif
        $response = json_decode( $response, true );

        // parcours du tableau associatif pour extraire les
        // entreprises à fort potentiel de recrutement en alternance dans la région d'Aix
        $annonces = array();
        foreach ( $response['lbaCompanies']['results'] as $entreprise){

            $id = $entreprise['company']['siret'];
            $title = $entreprise['title'];
            $body = $entreprise['nafs'][0]['label'].'; '.$entreprise['contact']['email'].'; '.$entreprise['place']['fullAddress'];

            $currentPost = new Post($id, $title, $body, date("Y-m-d H:i:s") );
            $annonces[$id] = $currentPost;
        }

        // enregistrement des annonces dans un fichier sur le serveur (serialisation)
        $annoncesSerialized = serialize($annonces);
        file_put_contents('data/cache_alternance', $annoncesSerialized);

        return $annonces;
    }

    public function getPost($id){

        $annoncesSerialized = file_get_contents('data/cache_alternance');
        $annonces = unserialize( $annoncesSerialized );

        return $annonces[$id];
    }
}