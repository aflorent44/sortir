
document.addEventListener("turbo:load", function () {
    const cityInput = document.getElementById("address_city");
    const postCodeInput = document.getElementById("address_zipCode")
    const streetInput = document.getElementById("address_street")
    const latInput = document.getElementById("address_lat")
    const lngInput = document.getElementById("address_lng")

    if (!cityInput || !postCodeInput) return;

    const citySuggestions = document.getElementById("suggestions-list");

    if (!cityInput.parentNode.querySelector(".suggestions-list")) {
        cityInput.parentNode.appendChild(citySuggestions);
    }

    const streetSuggestions = document.getElementById("street-suggestions");

    if (!streetInput.parentNode.querySelector(".suggestions-list")) {
        streetInput.parentNode.appendChild(streetSuggestions);
    }


    cityInput.removeEventListener("input", handleCityInput);
    cityInput.addEventListener("input", handleCityInput);

    streetInput.removeEventListener("input", handleStreetInput);
    streetInput.addEventListener("input", () => handleStreetInput(streetInput.value));

    document.removeEventListener("click", handleClickOutside);
    document.addEventListener("click", handleClickOutside);

    async function handleCityInput() {
        const query = cityInput.value.trim();

        if (query.length < 3) {
            citySuggestions.innerHTML = "";
            return;
        }

        try {
            const response = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${query}&type=municipality&limit=5`);
            const data = await response.json();
            citySuggestions.classList.remove("hidden")
            citySuggestions.innerHTML = "";

            data.features.forEach(feature => {
                const suggestion = document.createElement("li")
                if (feature.properties.type === "municipality"){
                    const cityName = feature.properties.city;
                    const cityPostcode = feature.properties.postcode || "";


                    suggestion.textContent = `${cityName} (${cityPostcode})`;
                    suggestion.dataset.city = cityName;
                    suggestion.dataset.postcode = cityPostcode;

                }
                suggestion.addEventListener("click", function () {
                    cityInput.value = this.dataset.city;
                    postCodeInput.value = this.dataset.postcode;


                    citySuggestions.innerHTML = "";
                });

                citySuggestions.appendChild(suggestion);
            });
        } catch (error) {
            console.error("Erreur lors de la récupération des données : ", error);
        }
    }

    async function handleStreetInput(query) {
        query = query.trim();
        const selectedCity = postCodeInput.value;
        if (query.length < 3 || !selectedCity) {
            streetSuggestions.innerHTML = "";
            return;
        }

        let apiUrl = `https://api-adresse.data.gouv.fr/search/?q=${query}&type=housenumber&postcode=${encodeURIComponent(selectedCity)}&limit=5`;

        try {
            const response = await fetch(apiUrl);
            const data = await response.json();
            streetSuggestions.classList.remove("hidden")
            streetSuggestions.innerHTML = "";
            console.log(apiUrl)

            data.features.forEach(feature => {
                const suggestionContent = feature.properties.housenumber + " " + feature.properties.street + ", " + feature.properties.city;
                const streetName = feature.properties.housenumber + " " + feature.properties.street
                const suggestion = document.createElement("li");
                suggestion.textContent = suggestionContent;
                suggestion.dataset.street = suggestionContent;

                suggestion.addEventListener("click", function () {
                    streetInput.value = streetName;
                    streetSuggestions.innerHTML = "";
                    latInput.value = feature.geometry.coordinates[1];
                    lngInput.value = feature.geometry.coordinates[0];
                });

                streetSuggestions.appendChild(suggestion);
            });
        } catch (error) {
            console.error("Erreur lors de la récupération des rues : ", error);
        }
    }

    function handleClickOutside(event) {
        if (!cityInput.contains(event.target) && !postCodeInput.contains(event.target) && !citySuggestions.contains(event.target)) {
            citySuggestions.classList.add("hidden")
            citySuggestions.innerHTML = "";
        }
        if (!streetInput.contains(event.target) && !streetSuggestions.contains(event.target)) {
            streetSuggestions.classList.add("hidden")
            streetSuggestions.innerHTML = "";
        }

    }
});
