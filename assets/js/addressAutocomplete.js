document.addEventListener("DOMContentLoaded", function () {
    const cityInput = document.getElementById("address_city");
    const zipCodeInput = document.getElementById("address_zipCode")
    const streetInput = document.getElementById("address_street")
    const latInput = document.getElementById("address_lat")
    const lngInput = document.getElementById("address_lng")

    if (!cityInput || !zipCodeInput) return;

    const citySuggestions = document.createElement("ul");
    citySuggestions.classList.add("suggestions-list");

    if (!cityInput.parentNode.querySelector(".suggestions-list")) {
        cityInput.parentNode.appendChild(citySuggestions);
    }
    if (!zipCodeInput.parentNode.querySelector(".suggestions-list") && !cityInput.parentNode.querySelector(".suggestions-list")) {
        zipCodeInput.parentNode.appendChild(citySuggestions);
    }

    const streetSuggestions = document.createElement("ul");
    streetSuggestions.classList.add("suggestions-list");

    if (!streetInput.parentNode.querySelector(".suggestions-list")) {
        streetInput.parentNode.appendChild(streetSuggestions);
    }


    cityInput.removeEventListener("input", handleCityInput);
    cityInput.addEventListener("input", handleCityInput);

    zipCodeInput.removeEventListener("input", handleCityInput);
    zipCodeInput.addEventListener("input", handleCityInput);

    streetInput.removeEventListener("input", handleStreetInput);
    streetInput.addEventListener("input", () => handleStreetInput(streetInput.value));

    document.removeEventListener("click", handleClickOutside);
    document.addEventListener("click", handleClickOutside);

    async function handleCityInput() {
        const query = cityInput.value.trim();

        if (query.length <= 3) {
            citySuggestions.innerHTML = "";
            return;
        }

        try {
            const response = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${query}&type=municipality&limit=5`);
            const data = await response.json();

            citySuggestions.innerHTML = "";

            data.features.forEach(feature => {
                const suggestionItem = document.createElement("li");
                if (feature.properties.type === "municipality"){
                const cityName = feature.properties.city;
                const cityPostcode = feature.properties.postcode || "";


                suggestionItem.textContent = `${cityName} (${cityPostcode})`;
                suggestionItem.dataset.city = cityName;
                suggestionItem.dataset.postcode = cityPostcode;

                }
                suggestionItem.addEventListener("click", function () {
                    cityInput.value = this.dataset.city;
                    zipCodeInput.value = this.dataset.postcode;


                    citySuggestions.innerHTML = "";
                });

                citySuggestions.appendChild(suggestionItem);
            });
        } catch (error) {
            console.error("Erreur lors de la récupération des données : ", error);
        }
    }

    async function handleStreetInput(query) {
        query = query.trim();
        const selectedCity = zipCodeInput.value;
        if (query.length <= 3 || !selectedCity) {
            streetSuggestions.innerHTML = "";
            return;
        }

        let apiUrl = `https://api-adresse.data.gouv.fr/search/?q=${query}&type=street&postcode=${encodeURIComponent(selectedCity)}&limit=5`;

        try {
            const response = await fetch(apiUrl);
            const data = await response.json();
            streetSuggestions.innerHTML = "";
            console.log(apiUrl)

            data.features.forEach(feature => {
                const streetName = feature.properties.street || feature.properties.name;

                const suggestionItem = document.createElement("li");
                suggestionItem.textContent = streetName;
                suggestionItem.dataset.street = streetName;

                suggestionItem.addEventListener("click", function () {
                    streetInput.value = this.dataset.street;
                    streetSuggestions.innerHTML = "";
                    latInput.value = feature.geometry.coordinates[1];
                    lngInput.value = feature.geometry.coordinates[0];
                });

                streetSuggestions.appendChild(suggestionItem);
            });
        } catch (error) {
            console.error("Erreur lors de la récupération des rues : ", error);
        }
    }

    function handleClickOutside(event) {
        if (!cityInput.contains(event.target) && !zipCodeInput.contains(event.target) && !citySuggestions.contains(event.target)) {
            citySuggestions.innerHTML = "";
        }
        if (!streetInput.contains(event.target) && !streetSuggestions.contains(event.target)) {
            streetSuggestions.innerHTML = "";
        }

    }
});
