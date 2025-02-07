<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S'inscrire</title>
    <link rel="stylesheet" href="css/signup.css">
</head>

<body>
    <div class="signup-container">

        <form action="../backend/signup.php" class="form" method="POST" enctype="multipart/form-data">
            <div class="signup-header">
                <h1>S'inscrire</h1>
            </div>


            <div class="pagination">

                <div class="number">
                    1
                </div>
                <div class="bar"></div>
                <div class="number">
                    2
                </div>
                <div class="bar"></div>
                <div class="number">
                    3
                </div>
            </div>
            <div class="steps">
                <div class="step">
                    <h2>Bienvenue !</h2>
                    <p>Avec HeartHive, trouvez du sens, faites des rencontres et engagez-vous dans des missions solidaires près de chez vous ! </p>

                    <label for="surname">Nom</label>
                    <input type="text" id="surname" name="surname" required>

                    <label for="name">Prénom</label>
                    <input type="text" id="name" name="name" required>

                    <label for="birth">Date de naissance</label>
                    <input type="date" id="birth" name="birth" required>







                </div>
                <div class="step">
                    <label for="city">Ville de résidence</label>
                    <div>
                        <input type="text" id="city" name="city" required>
                        <div id="suggestions" class="suggestions"></div>
                        <div id="map" style="height: 400px; width: 100%;"></div>
                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                const map = L.map('map').setView([46.603354, 1.888334], 6); // Center of France

                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                                }).addTo(map);

                                const input = document.getElementById("city");
                                const suggestions = document.getElementById("suggestions");

                                input.addEventListener("input", async () => {
                                    const query = input.value.trim();
                                    if (query.length < 2) {
                                        suggestions.innerHTML = "";
                                        return;
                                    }

                                    const response = await fetch(`https://geo.api.gouv.fr/communes?nom=${query}&fields=centre&boost=population&limit=5`);
                                    const data = await response.json();

                                    suggestions.innerHTML = "";
                                    data.forEach(commune => {
                                        const option = document.createElement("div");
                                        option.textContent = `${commune.nom} (${commune.departement.code})`;
                                        option.classList.add("suggestion");
                                        option.addEventListener("click", () => {
                                            input.value = commune.nom;
                                            suggestions.innerHTML = "";
                                            map.setView([commune.centre.coordinates[1], commune.centre.coordinates[0]], 13);
                                            L.marker([commune.centre.coordinates[1], commune.centre.coordinates[0]]).addTo(map)
                                                .bindPopup(`${commune.nom}`)
                                                .openPopup();
                                        });
                                        suggestions.appendChild(option);
                                    });
                                });
                            });
                        </script>
                    </div>
                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            const input = document.getElementById("city");
                            const suggestions = document.getElementById("suggestions");

                            input.addEventListener("input", async () => {
                                const query = input.value.trim();
                                if (query.length < 2) {
                                    suggestions.innerHTML = "";
                                    return;
                                }

                                const response = await fetch(`https://geo.api.gouv.fr/communes?nom=${query}&fields=departement&boost=population&limit=5`);
                                const data = await response.json();

                                suggestions.innerHTML = "";
                                data.forEach(commune => {
                                    const option = document.createElement("div");
                                    option.textContent = `${commune.nom} (${commune.departement.code})`;
                                    option.classList.add("suggestion");
                                    option.addEventListener("click", () => {
                                        input.value = commune.nom;
                                        suggestions.innerHTML = "";
                                    });
                                    suggestions.appendChild(option);
                                });
                            });
                        });
                    </script>
                    <style>
                        .suggestions {
                            width: auto;
                            border: 1px solid #ccc;
                            border-top: none;
                            max-height: 150px;
                            overflow-y: auto;
                            background-color: white;
                            position: relative;
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
                    </style>
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
                <button id="previous" disabled>Précédent</button>
                <button id="next">Suivant</button>
            </div>






        </form>

    </div>
    <script src="js/script.js"></script>
    <script src='js/signup.js'></script>
</body>

</html>