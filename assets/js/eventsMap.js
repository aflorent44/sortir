document.addEventListener("turbo:load", function () {
    let map = L.map('map').setView([47.2267626,-1.6206849], 8);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        minZoom: 3,
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);


    let popup = L.popup();

    function onMapClick(e) {
        popup
            .setLatLng(e.latlng)
            .setContent("You clicked the map at " + e.latlng.toString())
            .openOn(map);
    }

    //map.on('click', onMapClick);

    // Liste des événements récupérés depuis Twig
    function displayEvents(data) {
        console.log('Tous les événements:', data);

        data.forEach(event => {
            let lat = event.lat;
            let lng = event.lng;

            // Création du marqueur
            let marker = L.marker([lat, lng]).addTo(map);

            // Ajout d'un popup avec le nom de l'événement
            marker.bindPopup(`<b>${event.name}</b><br>${event.description}`);

            // Optionnel : Ajouter un événement au clic sur le marqueur
            marker.on('click', function () {
                window.location.href = `/event/${event.id}`;
            });
            marker.on('mouseover', function () {
                this.openPopup();
            });
            marker.on('mouseout', function () {
                this.closePopup();
            });

        });
    }


    function getAllEvents() {
        fetch('/api/events/')
            .then(response => response.json())
            .then(data => {

                // Manipulez les données comme vous le souhaitez
                displayEvents(data);
            })
            .catch(error => console.error('Erreur lors de la récupération des événements:', error));
    }

    getAllEvents();

});