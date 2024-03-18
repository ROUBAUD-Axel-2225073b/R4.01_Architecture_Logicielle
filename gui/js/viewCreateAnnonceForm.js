// récupération du boutton "localiser"
const localizeBtton = document.getElementById('localizeBtn');

localizeBtton.onclick = (e) => {

    // récupération du champ de saisie de localisation
    locationField = document.getElementById('location');

    navigator.geolocation.getCurrentPosition(async (position) => {
        // récupération de la latitude et de la longitude
        let lat = position.coords.latitude.toFixed(6);
        let long = position.coords.longitude.toFixed(6);

        // envoie d'une requête GET à l'API adresse du gouvernement
        let url = 'https://api-adresse.data.gouv.fr/reverse/?lon='+long+'&lat='+lat+'&type=locality';
        let response = await fetch(url);

        // recupération du résultat de la requête (un objet JSON)
        let result = await response.json();

        // ajout du nom de la ville dans le champ de saisie
        locationField.value = result.features[0].properties.city;
    });
};