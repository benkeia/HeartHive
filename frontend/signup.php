<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S'inscrire</title>
    <link rel="stylesheet" href="css/signup.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.css">
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js"></script>
    <style>
        .suggestions {
            width: auto;
            border: 1px solid #ccc;
            border-top: none;
            max-height: 150px;
            overflow-y: auto;
            background-color: white;
            position: absolute;
            border-radius: 5px;
            box-sizing: border-box;
            z-index: 1000;
        }

        .suggestion {
            padding: 10px;
            cursor: pointer;
        }

        .suggestion:hover {
            background-color: #f0f0f0;
        }

        .validation-icon {
            font-size: 1.2em;
            margin-left: 10px;
        }
    </style>
</head>

<body>
    <div class="signup-container">
        <form action="../backend/signup.php" class="form" method="POST" enctype="multipart/form-data">
            <div class="signup-header">
                <h1>S'inscrire</h1>
            </div>

            <div class="pagination">
                <div class="number">1</div>
                <div class="bar"></div>
                <div class="number">2</div>
                <div class="bar"></div>
                <div class="number">3</div>
            </div>

            <div class="steps">
                <div class="step">
                    <h2>Bienvenue !</h2>
                    <p>Avec HeartHive, trouvez du sens, faites des rencontres et engagez-vous dans des missions solidaires près de chez vous !</p>
                    <label for="surname">Nom</label>
                    <input type="text" id="surname" name="surname" required>

                    <label for="name">Prénom</label>
                    <input type="text" id="name" name="name" required>

                    <label for="birth">Date de naissance</label>
                    <input type="date" id="birth" name="birth" required>
                </div>

                <div class="step">
                    <label for="city-search">Ville de résidence</label>
                    <div style="display: flex; align-items: center;">
                        <input type="text" id="city-search" name="city" required>
                        <span id="validation-icon" class="validation-icon"></span>
                    </div>
                    <div id="suggestions" class="suggestions"></div>
                    <p>
                        <span>Dans un rayon de</span>
                        <span id="nb-km">0 km</span>
                    </p>
                    <input type="range" id="radius-range" min="0" max="100" value="0" step="10">

                    <div id="map" style="height: 300px; width: 100%;"></div>

                    <script>
                        let nbkm = document.getElementById("nb-km");

                        document.getElementById("radius-range").addEventListener("input", () => {
                            nbkm.textContent = document.getElementById("radius-range").value + " km";
                        });
                    </script>

                </div>

                <div class="step">
                    <label for="profile_picture">Photo de profil</label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required>

                    <label for="email">Adresse e-mail</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>

                    <label for="password_confirm">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>
            </div>

            <div class="signup-footer">
                <button type="button" id="previous" disabled>Précédent</button>
                <button type="button" id="next">Suivant</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const map = L.map("map").setView([46.603354, 1.888334], 6);
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                attribution: "© OpenStreetMap contributors",
                maxZoom: 18,
                zoomControl: false,
                scrollWheelZoom: false



            }).addTo(map);

            const citySearch = document.getElementById("city-search");
            const suggestions = document.getElementById("suggestions");
            const validationIcon = document.getElementById("validation-icon");

            let cityNames = [];

            citySearch.addEventListener("input", () => {
                const searchTerm = citySearch.value.trim();
                if (searchTerm.length > 2) {
                    fetch(`https://geo.api.gouv.fr/communes?nom=${searchTerm}&fields=nom,centre&boost=population&limit=5`)
                        .then(response => response.json())
                        .then(data => {
                            cityNames = data.map(commune => commune.nom);
                            displaySuggestions(data);
                            validateCity();
                        })
                        .catch(error => {
                            console.error("Erreur lors de la récupération des données:", error);
                        });
                } else {
                    suggestions.innerHTML = "";
                    validationIcon.innerHTML = "";
                }
            });

            let marker;

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
                        const {
                            coordinates
                        } = commune.centre;
                        if (marker) {
                            marker.setLatLng([coordinates[1], coordinates[0]]);
                        } else {
                            marker = L.marker([coordinates[1], coordinates[0]]).addTo(map);
                        }
                        map.setView(marker.getLatLng(), 12, {
                            animate: true
                        });
                        marker.bindPopup(commune.nom).openPopup();
                    });
                    suggestions.appendChild(item);
                });
            }

            function validateCity() {
                const inputCity = citySearch.value.trim();
                if (cityNames.includes(inputCity)) {
                    validationIcon.innerHTML = "✅";
                    validationIcon.style.color = "green";
                } else {
                    validationIcon.innerHTML = "❌";
                    validationIcon.style.color = "red";
                }
            }

            citySearch.addEventListener("blur", validateCity);
        });
    </script>
</body>

</html>