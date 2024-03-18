<?php

namespace data;

use domain\Post;
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
        $query ='?romes='.$romes.'&caller=contact%40domaine%20nom_de_societe&latitude='.$latitudeAix.'&longitude='.$longitudeAix.'&radius='.$radius.'&insee='.$inseeAix;

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
        // /!\ modifications au code de l'énoncé: si il manque des informations, on évite de les afficher pour éviter les erreurs
        // /!\ modifications au code de l'énoncé: on utilise l'index comme identifiant unique pour lister toutes les annonces (la version de l'énoncé n'en affiche qu'une ?)
        $annonces = array();
        foreach ($response['peJobs']['results'] as $index => $entreprise) {
            $id = $index; // Using the index as the unique identifier
            $title = $entreprise['title'];

            // Check if 'nafs', 'contact', and 'place' are set and not null
            $nafsLabel = $entreprise['nafs'][0]['label'] ?? '';
            $email = $entreprise['contact']['email'] ?? '';
            $fullAddress = $entreprise['place']['fullAddress'] ?? '';

            // Construct body, handling potential null values
            $body = $nafsLabel . '; ' . $email . '; ' . $fullAddress;

            $currentPost = new Post($id, $title, $body, date("Y-m-d H:i:s"));
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