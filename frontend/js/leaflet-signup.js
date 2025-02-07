
document.addEventListener("DOMContentLoaded", () => {

    var customIcon = L.icon({
        iconUrl: 'assets/icons/target.png', // Remplace par le chemin de ton image
        iconSize: [20, 20], // Taille de l'icône [largeur, hauteur]
        iconAnchor: [10, 10], // Point d'ancrage de l'icône
        popupAnchor: [0, -40] // Position du popup par rapport à l'icône
    });

    const map = L.map("map", {
        scrollWheelZoom: false,
        zoomControl: false
    }).setView([46.603354, 1.888334], 6);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "© OpenStreetMap contributors",
    }).addTo(map);

    const citySearch = document.getElementById("city-search");
    const suggestions = document.getElementById("suggestions");
    const validationIcon = document.getElementById("validation-icon");
    const radiusRange = document.getElementById("radius-range");
    const nbkm = document.getElementById("nb-km");

    let marker, circle, cityGeoJSON, currentCityCode;

    citySearch.addEventListener("input", () => {
        const searchTerm = citySearch.value.trim();
        if (searchTerm.length > 2) {
            fetch(`https://geo.api.gouv.fr/communes?nom=${searchTerm}&fields=nom,centre,code&boost=population&limit=5`)
                .then(response => response.json())
                .then(data => {
                    displaySuggestions(data);
                })
                .catch(error => {
                    console.error("Erreur lors de la récupération des données:", error);
                });
        } else {
            suggestions.innerHTML = "";
            validationIcon.innerHTML = "";
        }
    });

    function displaySuggestions(communes) {
        suggestions.innerHTML = "";
        communes.forEach(commune => {
            const item = document.createElement("div");
            item.className = "suggestion";
            item.textContent = commune.nom;
            item.addEventListener("click", () => {
                citySearch.value = commune.nom;
                validationIcon.innerHTML = "✅";
                validationIcon.style.color = "green";
                suggestions.innerHTML = "";
                setCityLocation(commune);
            });
            suggestions.appendChild(item);
        });
    }

    function setCityLocation(commune) {
        const {
            coordinates
        } = commune.centre;
        if (marker) marker.remove();
        if (circle) circle.remove();
        if (cityGeoJSON) cityGeoJSON.remove();

        marker = L.marker([coordinates[1], coordinates[0]], {
            icon: customIcon
        }).addTo(map);
        map.setView(marker.getLatLng(), 12, {
            animate: false
        });

        currentCityCode = commune.code;
        updateCircle();
    }

    function updateCircle() {
        const radius = parseInt(radiusRange.value) * 1000;
        if (circle) circle.remove();
        if (cityGeoJSON) cityGeoJSON.remove();

        if (radius === 0 && currentCityCode) {
            fetch(`https://geo.api.gouv.fr/communes/${currentCityCode}?fields=contour`)
                .then(response => response.json())
                .then(data => {
                    cityGeoJSON = L.geoJSON(data.contour, {
                        color: "#0C5193"
                    }).addTo(map);
                    setTimeout(() => map.fitBounds(cityGeoJSON.getBounds()), 100);
                })
                .catch(error => {
                    console.error("Erreur lors du chargement du contour de la ville:", error);
                });
        } else if (marker) {
            circle = L.circle(marker.getLatLng(), {
                radius,
                color: "#0C5193",
            }).addTo(map);
            setTimeout(() => map.fitBounds(circle.getBounds()), 100);
        }
    }

    radiusRange.addEventListener("input", () => {
        nbkm.textContent = radiusRange.value + " km";
        updateCircle();
    });
});
