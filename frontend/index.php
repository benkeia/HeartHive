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

// Récupérer les missions avec les données des associations associées
$sql = "SELECT m.*, a.association_name, a.association_adress 
        FROM missions m 
        JOIN association a ON m.association_id = a.association_id 
        ORDER BY m.created_at DESC";

$result = $conn->query($sql);

$missions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Vérifier si l'association a une adresse valide
        if ($row['association_adress'] === null) {
            // On vérifie si la mission a sa propre localisation
            if ($row['location'] !== null) {
                $location = json_decode($row['location'], true);
                if (isset($location['coordinates'])) {
                    $mission_lat = $location['coordinates'][1];
                    $mission_lon = $location['coordinates'][0];
                } else {
                    continue; // Pas de coordonnées valides
                }
            } else {
                continue; // Ni l'asso ni la mission n'ont d'adresse
            }
        } else {
            // Utiliser l'adresse de l'association
            $association_adress = json_decode($row['association_adress'], true);
            if (!isset($association_adress['coordinates'])) {

                // On vérifie si la mission a sa propre localisation
                if ($row['location'] !== null) {
                    $location = json_decode($row['location'], true);
                    if (isset($location['coordinates'])) {
                        $mission_lat = $location['coordinates'][1];
                        $mission_lon = $location['coordinates'][0];
                    } else {
                        continue; // Pas de coordonnées valides
                    }
                } else {
                    continue; // Pas de coordonnées valides
                }
            } else {
                $mission_lat = $association_adress['coordinates'][1];
                $mission_lon = $association_adress['coordinates'][0];
            }
        }

        // Calculer la distance
        $distance = haversine($user_lat, $user_lon, $mission_lat, $mission_lon);

        $row['distance'] = $distance;
        $row['within_range'] = $distance <= $user_range ? '✅' : '❌';

        // Analyser les compétences et les tags
        $row['skills_required'] = !empty($row['skills_required']) ? json_decode($row['skills_required'], true) : [];
        $row['tags'] = !empty($row['tags']) ? json_decode($row['tags'], true) : [];

        $missions[] = $row;
    }

    // Trier les missions par distance
    usort($missions, fn($a, $b) => $a['distance'] <=> $b['distance']);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HeartHive - Missions de Bénévolat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="frontend/assets/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <h1 class="px-10 text-4xl font-bold mt-10">HeartHive</h1>

    <div class="main-container flex flex-row">
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
                            <option value="volunteers_needed">Places disponibles</option>
                        </select>

                        <label>Tags</label>
                        <div class="categories flex flex-wrap gap-2">
                            <?php
                            // Récupérer tous les tags uniques de toutes les missions
                            $allTags = [];
                            foreach ($missions as $mission) {
                                if (is_array($mission['tags'])) {
                                    $allTags = array_merge($allTags, $mission['tags']);
                                }
                            }
                            $uniqueTags = array_unique($allTags);
                            sort($uniqueTags);

                            // Afficher les 10 premiers tags
                            $displayedTags = array_slice($uniqueTags, 0, 6);
                            foreach ($displayedTags as $tag) {
                                echo '<button class="category-btn flex items-center gap-1 p-2 border border-gray-300 rounded-full bg-white cursor-pointer text-sm" data-tag="' . htmlspecialchars($tag) . '">' . htmlspecialchars($tag) . '</button>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-center items-center mt-20 w-3/4">
            <div class="card-container flex gap-x-10 gap-y-5 flex-row flex-wrap w-2/3">
                <?php if (!empty($missions)): ?>
                    <?php foreach ($missions as $mission): ?>
                        <a class="transform w-72 h-[400px] bg-white rounded-lg shadow-md flex flex-col hover:shadow-lg transition-all duration-500 hover:scale-110 hover:bg-slate-100 mission-link"
                            href="mission.php?id=<?= htmlspecialchars($mission['mission_id']) ?>" data-id="<?= htmlspecialchars($mission['mission_id']) ?>">
                            <img src="<?= htmlspecialchars($mission['image_url'] ?: 'assets/uploads/background_image/defaultMission.jpg') ?>" alt="<?= htmlspecialchars($mission['title']) ?>"
                                class="w-full h-40 object-cover rounded-t-md">
                            <div class="p-5 flex flex-col h-full">
                                <div class="flex justify-between items-start">
                                    <h2 class="text-lg font-bold"><?= htmlspecialchars($mission['title']) ?></h2>
                                    <p class="text-sm text-gray-500"><?= round($mission['distance'], 1) ?> km</p>
                                </div>

                                <p class="text-sm text-gray-500 mt-1">Par <?= htmlspecialchars($mission['association_name']) ?></p>

                                <div class="flex flex-wrap gap-1 mt-2">
                                    <?php if (!empty($mission['tags']) && is_array($mission['tags'])): ?>
                                        <?php foreach (array_slice($mission['tags'], 0, 3) as $tag): ?>
                                            <span class="bg-pink-100 text-pink-800 text-xs px-2 py-1 rounded-full"><?= htmlspecialchars($tag) ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <p class="text-sm text-gray-700 mt-2 line-clamp-3 flex-grow">
                                    <?= htmlspecialchars($mission['description']) ?>
                                </p>

                                <div class="mt-auto pt-2 border-t border-gray-100 flex justify-between items-center">
                                    <span class="text-xs text-gray-500">
                                        <?= date('d/m/Y', strtotime($mission['created_at'])) ?>
                                    </span>
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                        <?= intval($mission['volunteers_registered']) ?>/<?= intval($mission['volunteers_needed']) ?> bénévoles
                                    </span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune mission trouvée.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php echo '<script>const userDefaultCity = "' . htmlspecialchars($user_adress['name'] ?? '') . '";</script>'; ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const rangeInput = document.getElementById("distance");
            const distanceValue = document.getElementById("distanceValue");
            const cityInput = document.getElementById("city");
            const sortSelect = document.getElementById("sort");
            const categoryBtns = document.querySelectorAll(".category-btn");
            let debounceTimer;
            let selectedTags = [];

            // Fonction pour attacher les événements de clic aux missions
            function attachMissionEventListeners() {
                document.querySelectorAll(".mission-link").forEach(link => {
                    link.addEventListener("click", function(event) {
                        event.preventDefault();
                        let missionId = this.getAttribute("data-id");
                        // Rediriger directement vers mission.php avec l'ID en paramètre
                        window.location.href = "mission.php?id=" + missionId;
                    });
                });
            }

            // Gestion des filtres par tag
            categoryBtns.forEach(btn => {
                btn.addEventListener("click", function() {
                    const tag = this.getAttribute("data-tag");

                    // Basculer la sélection du tag
                    if (this.classList.contains("selected")) {
                        this.classList.remove("selected", "bg-pink-100", "border-pink-300", "text-pink-800");
                        selectedTags = selectedTags.filter(t => t !== tag);
                    } else {
                        this.classList.add("selected", "bg-pink-100", "border-pink-300", "text-pink-800");
                        selectedTags.push(tag);
                    }

                    updateMissions();
                });
            });

            // Pré-remplir le champ avec l'adresse de l'utilisateur si disponible
            if (typeof userDefaultCity !== 'undefined' && userDefaultCity) {
                cityInput.value = userDefaultCity;
            }

            // Mise à jour de l'affichage de la distance
            rangeInput.value = <?= $user_range ?? 50 ?>;
            distanceValue.textContent = `${rangeInput.value} km`;

            // Gestion de la barre de distance avec incréments de 10km
            rangeInput.addEventListener("input", function() {
                const value = Math.round(this.value / 10) * 10;
                distanceValue.textContent = `${value} km`;

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    this.value = value;
                    updateMissions();
                }, 500);
            });

            // Gestion du champ ville avec debounce
            cityInput.addEventListener("input", function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    updateMissions();
                }, 1000);
            });

            // Gestion du tri
            sortSelect.addEventListener("change", updateMissions);

            function getUserLocation(callback) {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            callback({
                                lat: position.coords.latitude,
                                lon: position.coords.longitude
                            });
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

            function updateMissions() {
                let city = cityInput.value.trim();
                let distance = Math.round(rangeInput.value / 10) * 10;
                let sort = sortSelect.value;

                if (city) {
                    if (city.length < 3) return; // Empêche les requêtes inutiles

                    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(city)}`, {
                            headers: {
                                "User-Agent": "HeartHive/1.0 (barryvert@gmail.com)"
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.length > 0) {
                                fetchFilteredMissions(data[0].lat, data[0].lon, distance, sort);
                            } else {
                                console.warn("Aucune correspondance pour la ville saisie.");
                            }
                        })
                        .catch(error => console.error("Erreur lors de la récupération des coordonnées :", error));
                } else {
                    getUserLocation((userCoords) => {
                        if (userCoords) {
                            fetchFilteredMissions(userCoords.lat, userCoords.lon, distance, sort);
                        } else {
                            console.warn("Impossible d'utiliser la géolocalisation.");
                        }
                    });
                }
            }

            function fetchFilteredMissions(lat, lon, distance, sort) {
                const container = document.querySelector(".card-container");
                container.innerHTML = "<p class='text-center w-full'>Chargement des missions...</p>";

                fetch("filter_missions.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: `lat=${lat}&lon=${lon}&distance=${distance}&sort=${sort}&tags=${JSON.stringify(selectedTags)}`
                    })
                    .then(response => response.json())
                    .then(updateMissionsUI)
                    .catch(error => {
                        console.error("Erreur lors du chargement des missions :", error);
                        container.innerHTML = "<p class='text-center w-full text-red-500'>Erreur lors du chargement des missions</p>";
                    });
            }

            function updateMissionsUI(data) {
                const container = document.querySelector(".card-container");
                container.innerHTML = "";

                if (data.length === 0) {
                    container.innerHTML = "<p class='text-center w-full'>Aucune mission trouvée avec ces critères.</p>";
                    return;
                }

                data.forEach(mission => {
                    let sortMessage = '';
                    if (sortSelect.value === "closest") {
                        sortMessage = `${mission.distance.toFixed(1)} km`;
                    } else if (sortSelect.value === "recent") {
                        sortMessage = new Date(mission.created_at).toLocaleDateString();
                    } else {
                        sortMessage = `${mission.volunteers_registered}/${mission.volunteers_needed}`;
                    }

                    // Créer les tags
                    let tagsHTML = '';
                    if (mission.tags && Array.isArray(JSON.parse(mission.tags))) {
                        const tags = JSON.parse(mission.tags);
                        tags.slice(0, 3).forEach(tag => {
                            tagsHTML += `<span class="bg-pink-100 text-pink-800 text-xs px-2 py-1 rounded-full">${tag}</span>`;
                        });
                    }

                    let missionHTML = `
                    <a class="transform w-72 h-[400px] bg-white rounded-lg shadow-md flex flex-col hover:shadow-lg transition-all duration-500 hover:scale-110 hover:bg-slate-100 mission-link"
                       href="mission.php?id=${mission.mission_id}" data-id="${mission.mission_id}">
                        <img src="${mission.image_url || 'assets/uploads/background_image/defaultMission.jpg'}" alt="${mission.title}"
                             class="w-full h-40 object-cover rounded-t-md">
                        <div class="p-5 flex flex-col h-full">
                            <div class="flex justify-between items-start">
                                <h2 class="text-lg font-bold">${mission.title}</h2>
                                <p class="text-sm text-gray-500">${sortMessage}</p>
                            </div>
                            
                            <p class="text-sm text-gray-500 mt-1">Par ${mission.association_name}</p>
                            
                            <div class="flex flex-wrap gap-1 mt-2">
                                ${tagsHTML}
                            </div>
                            
                            <p class="text-sm text-gray-700 mt-2 line-clamp-3 flex-grow">
                                ${mission.description}
                            </p>
                            
                            <div class="mt-auto pt-2 border-t border-gray-100 flex justify-between items-center">
                                <span class="text-xs text-gray-500">
                                    ${new Date(mission.created_at).toLocaleDateString()}
                                </span>
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                    ${mission.volunteers_registered}/${mission.volunteers_needed} bénévoles
                                </span>
                            </div>
                        </div>
                    </a>`;
                    container.insertAdjacentHTML("beforeend", missionHTML);
                });

                // Réattacher les événements de clic sur les nouvelles missions
                attachMissionEventListeners();
            }

            // Attacher les événements aux liens de mission existants
            attachMissionEventListeners();
        });
    </script>
</body>

</html>