document.addEventListener("turbo:load", function () {

    const mapContainer = document.getElementById('map');
    if (!mapContainer) {
        console.log("Conteneur de carte 'map' non trouvé sur cette page");
        return; // Sortir de la fonction si le conteneur n'existe pas
    }

    let map; // Déclarer `map` au niveau global de l'événement pour qu'il soit accessible dans toutes les fonctions

    function displayMap() {
        map = L.map('map').setView([47.2267626, -1.6206849], 6);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            minZoom: 3,
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
    }

    function displayEvents(data) {
        data.forEach(event => {
            let lat = event.lat;
            let lng = event.lng;

            // Création du marqueur
            let marker = L.marker([lat, lng]).addTo(map);

            // Ajout d'un popup avec le nom de l'événement
            marker.bindPopup(`<b>${event.name}</b><br>${event.description}`);

            // Gérer les événements au survol et au clic
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
            .then(response => {
                if (!response.ok) {
                    throw new Error("Problème lors du chargement des sorties");
                }
                return response.json();
            })
            .then(data => {
                if (data.length === 0) {
                    console.warn("Aucune sortie trouvée !");
                }
                displayEvents(data);
            })
            .catch(error => {
                console.error('Erreur lors de la récupération des sorties : ', error);
                alert("Impossible de charger les sorties. Veuillez réessayer plus tard.");
            });
    }

    // Appel immédiat des fonctions au chargement de la page
    displayMap();
    getAllEvents();
});


document.addEventListener("turbo:load", function () {

    const eventMapContainer = document.getElementById('event-map');
    const eventDataElement = document.getElementById('event-data');

    if (!eventMapContainer || !eventDataElement) {
        console.log("Conteneurs nécessaires non trouvés sur cette page");
    } else {
        console.log("fuegfyuik")
    }

    let eventMap; // Déclaration au niveau global de l'événement

    // Récupérer l'ID de l'événement depuis la page
    // Par exemple, si vous avez un élément avec data-event-id
    const eventId = document.getElementById('event-data').dataset.eventId;
    // Ou vous pourriez l'extraire de l'URL

    function displayEventMap(lat, lng, eventName) {
        eventMap = L.map('event-map').setView([lat, lng], 12);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            minZoom: 3,
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(eventMap);

        console.log(lat, lng);
        // Ajout du marqueur
        L.marker([lat, lng]).addTo(eventMap)
            .bindPopup(`<b>${eventName}</b>`)
            .openPopup();
    }

    function getEvent() {
        fetch(`/api/events/${eventId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error("Problème lors du chargement de la sortie");
                }
                return response.json();
            })
            .then(data => {
                if (data.length === 0) {
                    console.warn("Aucune sortie trouvée !");
                    return;
                }
                console.log(data);
                // Maintenant que nous avons les données, nous pouvons afficher la carte
                const event = data[0]; // Vous renvoyez un tableau avec un seul élément
                displayEventMap(event.lat, event.lng, event.name);
            })
            .catch(error => {
                console.error('Erreur lors de la récupération de la sortie : ', error);
                alert("Impossible de charger la sortie. Veuillez réessayer plus tard.");
            });
    }

    // N'appelez que getEvent(), qui appellera displayEventMap() une fois les données récupérées
    getEvent();
});

