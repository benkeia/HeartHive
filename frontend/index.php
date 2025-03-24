<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="frontend/assets/css/style.css">

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<?php
include '../backend/db.php';
include 'include/header.php';

if (!isset($_SESSION['mail'])) {
    echo "Veuillez vous connecter pour voir les recommandations.";
    exit;
}

// Récupération des coordonnées de l'utilisateur
$user_mail = $_SESSION['mail'];
$sql_user = "SELECT user_adress FROM user WHERE user_mail = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("s", $user_mail);
$stmt->execute();
$result_user = $stmt->get_result();
$user_data = $result_user->fetch_assoc();

if (!$user_data || empty($user_data['user_adress'])) {
    echo "Coordonnées de l'utilisateur introuvables.";
    exit;
}

$user_adress = json_decode($user_data['user_adress'], true);
if (!isset($user_adress['coordinates']) || !isset($user_adress['range'])) {
    echo "Format d'adresse invalide.";
    
    exit;
}
$user_lat = $user_adress['coordinates'][1];
$user_lon = $user_adress['coordinates'][0];
$user_range = $user_adress['range']; // Rayon de recherche de l'utilisateur

function haversine($lat1, $lon1, $lat2, $lon2)
{
    $earth_radius = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earth_radius * $c;
}

// Récupérer les associations
$sql = "SELECT association_id, association_name, association_siren, association_adress, association_mail, association_date, 
               association_profile_picture, association_mission 
        FROM association";
$result = $conn->query($sql);

$associations = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $association_adress = json_decode($row['association_adress'], true);
        if (!isset($association_adress['coordinates'])) {
            continue;
        }

        $assoc_lat = $association_adress['coordinates'][1];
        $assoc_lon = $association_adress['coordinates'][0];
        $distance = haversine($user_lat, $user_lon, $assoc_lat, $assoc_lon);

        $row['distance'] = $distance;
        $row['within_range'] = $distance <= $user_range ? '✅ Oui' : '❌ Non';
        $associations[] = $row;
    }

    // Trier les associations par distance
    usort($associations, fn($a, $b) => $a['distance'] <=> $b['distance']);
}
?>


<body>
    <h1 class="px-10 text-4xl font-bold mt-10">HeartHive</h1>

    <div class="main-container flex flex-row ">
    <div class="container w-1/4 flex justify-end">
        <div class="row">
            <div class="col-3">
                <div class="filtres flex flex-col gap-4 p-5 bg-white rounded-lg shadow-md w-72 mt-28">
                    <h2 class="mb-2">Filtres</h2>
        
                    <label for="city">Ville</label>
                    <input type="text" id="city" placeholder="Rechercher une ville..." class="w-full p-2 border border-gray-300 rounded-md">
        
                    <label for="distance">Distance</label>
                    <div class="slider-container flex items-center gap-2">
                        <input type="range" id="distance" min="1" max="200" value="10" class="flex-grow" step="10">
                        <span id="distanceValue">50km</span>
                    </div>
        
                    <label for="sort">Trier par</label>
                    <select id="sort" class="w-full p-2 border border-gray-300 rounded-md">
                        <option value="closest">Le plus proche</option>
                        <option value="recent">Le plus récent</option>
                    </select>
        
                    <label>Catégories</label>
                    <div class="categories flex flex-wrap gap-2">
                        <button class="category-btn flex items-center gap-1 p-2 border border-gray-300 rounded-full bg-white cursor-pointer text-sm">🎨 Art</button>
                        <button class="category-btn flex items-center gap-1 p-2 border border-gray-300 rounded-full bg-white cursor-pointer text-sm">🎵 Musique</button>
                        <button class="category-btn flex items-center gap-1 p-2 border border-gray-300 rounded-full bg-white cursor-pointer text-sm">⚽ Sport</button>
                        <button class="category-btn flex items-center gap-1 p-2 border border-gray-300 rounded-full bg-white cursor-pointer text-sm">❤️ Humanitaire</button>
                        <button class="category-btn flex items-center gap-1 p-2 border border-gray-300 rounded-full bg-white cursor-pointer text-sm">⚖️ Droits / Inclusion</button>
                        <button class="category-btn flex items-center gap-1 p-2 border border-gray-300 rounded-full bg-white cursor-pointer text-sm">📚 Enseignement</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex justify-center items-center mt-20 w-3/4">
        <div class="card-container flex gap-x-10 gap-y-5 flex-row flex-wrap w-2/3">
            <?php if (!empty($associations)): ?>
                <?php foreach ($associations as $association): ?>
                    <?php
                    $association_adress = json_decode($association['association_adress'], true);
                    $coordinates = implode(', ', $association_adress['coordinates']);
                    ?>
                    <a class="transform w-72 h-[400px] bg-white rounded-lg shadow-md flex flex-col hover:shadow-lg transition-all duration-500 hover:scale-110 hover:bg-slate-100 association-link"
                        href="association.php" data-id="<?= htmlspecialchars($association['association_id']) ?>">
                        <img src="assets/uploads/background_image/defaultAssociation.jpg" alt="Illustration"
                            class="w-full rounded-md">
                        <div class="p-5">
                            <div class="flex justify-between items-start mt-3">
                                <h2 class="text-lg font-bold"><?= htmlspecialchars($association['association_name']) ?></h2>
                                <p class="text-sm text-gray-500"><?= round($association['distance'], 2) ?>
                                    km<?= $association['within_range'] ?></p>
                            </div>

                            <p class="bg-gray-200 text-sm px-3 py-1 rounded-full mt-2 w-max">📖 Tutorat</p>

                            <p class="text-sm text-gray-700 mt-2 line-clamp-6">
                                <?= htmlspecialchars($association['association_mission']) ?>
                            </p>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune association trouvée.</p>
            <?php endif; ?>
        </div>
    </div>
    </div>
    <?php echo '<script>const userDefaultCity = "' . htmlspecialchars($user_adress['name']) . '";</script>';?>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const rangeInput = document.getElementById("distance");
    const distanceValue = document.getElementById("distanceValue");
    const cityInput = document.getElementById("city");
    const sortSelect = document.getElementById("sort");
    let debounceTimer;

     // Pré-remplir le champ avec l'adresse de l'utilisateur
     if (typeof userDefaultCity !== 'undefined' && userDefaultCity) {
        cityInput.value = userDefaultCity;
    }
    // Met à jour l'affichage de la distance
    distanceValue.textContent = `${rangeInput.value} km`;
    
    // Gestion de la barre de distance avec incréments de 10km
    rangeInput.addEventListener("input", function () {
        // Arrondir à la dizaine la plus proche
        const value = Math.round(this.value / 10) * 10;
        
        // Mettre à jour l'affichage immédiatement
        distanceValue.textContent = `${value} km`;
        
        // Debouncer l'appel à la fonction de filtrage
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            // S'assurer que la valeur est bien un multiple de 10
            this.value = value;
            updateAssociations();
        }, 500);
    });

    // Gestion du champ ville avec debounce
    cityInput.addEventListener("input", function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            updateAssociations();
        }, 1000); // Attendre 1 seconde après la fin de la saisie
    });

    // Gestion du tri sans debounce (changement immédiat)
    sortSelect.addEventListener("change", function() {
        updateAssociations();
    });

    function getUserLocation(callback) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const userCoords = { lat: position.coords.latitude, lon: position.coords.longitude };
                    callback(userCoords);
                },
                () => {
                    console.warn("Impossible d'obtenir la localisation.");
                    callback(null);
                }
            );
        } else {
            console.warn("Géolocalisation non supportée.");
            callback(null);
        }
    }

    function updateAssociations() {
        let city = cityInput.value.trim();
        let distance = Math.round(rangeInput.value / 10) * 10; // Garantir un multiple de 10
        let sort = sortSelect.value; // "closest" ou "recent"

        if (city) {
            // Ne lance la requête que si la ville contient au moins 3 caractères
            if (city.length < 3) {
                return; // Ne pas lancer de requête si trop peu de caractères
            }
            
            // Ajouter un header User-Agent comme recommandé par Nominatim
            const headers = {
                "User-Agent": "HeartHive/1.0 barryvert@gmail.com)"
            };
            
            // Géocoder l'adresse saisie
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(city)}`, {
                headers: headers
            })
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        let cityLat = data[0].lat;
                        let cityLon = data[0].lon;
                        fetchFilteredAssociations(cityLat, cityLon, distance, sort);
                    } else {
                        console.warn("Aucune correspondance pour la ville saisie.");
                    }
                })
                .catch(error => console.error("Erreur lors de la récupération des coordonnées :", error));
        } else {
            // Utiliser la localisation de l'utilisateur
            getUserLocation((userCoords) => {
                if (userCoords) {
                    fetchFilteredAssociations(userCoords.lat, userCoords.lon, distance, sort);
                } else {
                    console.warn("Impossible d'utiliser la géolocalisation.");
                }
            });
        }
    }

    function fetchFilteredAssociations(lat, lon, distance, sort) {
        // Afficher un indicateur de chargement si nécessaire
        const container = document.querySelector(".card-container");
        container.innerHTML = "<p class='text-center w-full'>Chargement des associations...</p>";
        
        fetch("filter_associations.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `lat=${lat}&lon=${lon}&distance=${distance}&sort=${sort}`
        })
            .then(response => response.json())
            .then(updateAssociationsUI)
            .catch(error => {
                console.error("Erreur lors du chargement des associations :", error);
                container.innerHTML = "<p class='text-center w-full text-red-500'>Erreur lors du chargement des associations</p>";
            });
    }

    function updateAssociationsUI(data) {
        const container = document.querySelector(".card-container");
        container.innerHTML = "";

        if (data.length === 0) {
            container.innerHTML = "<p class='text-center w-full'>Aucune association trouvée avec ces critères.</p>";
            return;
        }

        data.forEach(association => {
            // Déterminer le message de tri approprié
            let sortMessage = "";
            if (sortSelect.value === "closest") {
                sortMessage = `${association.distance.toFixed(2)} km`;
            } else if (sortSelect.value === "recent") {
                // Si vous avez une date de création dans vos données
                sortMessage = association.created_at ? new Date(association.created_at).toLocaleDateString() : "";
            }

            let associationHTML = `
                <a class="transform w-72 h-[400px] bg-white rounded-lg shadow-md flex flex-col hover:shadow-lg transition-all duration-500 hover:scale-110 hover:bg-slate-100 association-link"
                   href="association.php?id=${association.association_id}" data-id="${association.association_id}">
                    <img src="${association.image_url || 'assets/uploads/background_image/defaultAssociation.jpg'}" alt="Illustration"
                         class="w-full rounded-md">
                    <div class="p-5">
                        <div class="flex justify-between items-start mt-3">
                            <h2 class="text-lg font-bold">${association.association_name}</h2>
                            <p class="text-sm text-gray-500">${sortMessage}</p>
                        </div>
                        <p class="bg-gray-200 text-sm px-3 py-1 rounded-full mt-2 w-max">📖 ${association.category || 'Tutorat'}</p>
                        <p class="text-sm text-gray-700 mt-2 line-clamp-6">${association.association_mission}</p>
                    </div>
                </a>`;
            container.insertAdjacentHTML("beforeend", associationHTML);
        });

        // Ajouter des écouteurs d'événements pour les liens d'association si nécessaire
        document.querySelectorAll('.association-link').forEach(link => {
            link.addEventListener('click', function(e) {
                // Si vous avez besoin de code spécifique au clic, ajoutez-le ici
            });
        });
    }

    // Lancer la recherche initiale au chargement de la page
    updateAssociations();
});
    </script>

    

    <script type="module" src="../node_modules/dropzone/dist/dropzone-min.js"></script>
    <script type="module" src="js/script.js"></script>
</body>

</html>