document.addEventListener("DOMContentLoaded", function () {
    const cityInput = document.getElementById("address_city");
    const zipCodeInput = document.getElementById("address_zipCode")

    if (!cityInput || !zipCodeInput) return;

    const suggestionsContainer = document.createElement("ul");
    suggestionsContainer.classList.add("suggestions-list");

    if (!cityInput.parentNode.querySelector(".suggestions-list")) {
        cityInput.parentNode.appendChild(suggestionsContainer);
    }

    cityInput.removeEventListener("input", handleCityInput);
    cityInput.addEventListener("input", handleCityInput);

    document.removeEventListener("click", handleClickOutside);
    document.addEventListener("click", handleClickOutside);

    async function handleCityInput() {
        const query = cityInput.value.trim();

        if (query.length < 3) {
            suggestionsContainer.innerHTML = ""; // Efface les suggestions si trop court
            return;
        }

        try {
            const response = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${query}&&type=municipality&limit=5`);
            const data = await response.json();

            suggestionsContainer.innerHTML = "";

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
                    suggestionsContainer.innerHTML = "";
                });

                suggestionsContainer.appendChild(suggestionItem);
            });
        } catch (error) {
            console.error("Erreur lors de la récupération des données : ", error);
        }
    }

    function handleClickOutside(event) {
        if (!cityInput.contains(event.target) && !suggestionsContainer.contains(event.target)) {
            suggestionsContainer.innerHTML = "";
        }
    }
});
