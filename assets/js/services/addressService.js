
document.addEventListener("DOMContentLoaded", function () {
    console.log ("coucou")
    const input = document.getElementById("address");
    const suggestions = document.getElementById("suggestions");

    if (!input || !suggestions) return;

    input.addEventListener("input", function () {
        console.log("input détécté")
        const query = input.value.trim();
        if (query.length < 2) {
            suggestions.innerHTML = "";
            return;
        }

        fetch(`/api/address?q=${query}`)
            .then(response => response.json())
            .then(data => {
                suggestions.innerHTML = "";

                data.forEach(city => {
                    const li = document.createElement("li");
                    li.textContent = `${city.nom} (${city.code})`;
                    li.addEventListener("click", function () {
                        console.log("click")
                        input.value = city.nom;
                        suggestions.innerHTML = "";
                    });
                    suggestions.appendChild(li);
                });
            })
            .catch(error => console.error("Erreur API : ", error));
    });
});