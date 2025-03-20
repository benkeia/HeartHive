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
    <h1>HeartHive</h1>

    <p>Recommandations d'associations proches de vous (Rayon : <?= htmlspecialchars($user_range) ?> km) :</p>
    <div class="main-container flex flex-row ">
    <div class="container w-1/4">
        <div class="row">
            <div class="col-3">
                <div class="filtres">
                    <h2>Filtres</h2>
        
                    <label for="city">Ville</label>
                    <input type="text" id="city" placeholder="Rechercher une ville...">
        
                    <label for="distance">Distance</label>
                    <div class="slider-container">
                        <input type="range" id="distance" min="1" max="100" value="50">
                        <span id="distanceValue">50km</span>
                    </div>
        
                    <label for="sort">Trier par</label>
                    <select id="sort">
                        <option value="closest">Le plus proche</option>
                        <option value="recent">Le plus récent</option>
                    </select>
        
                    <label>Catégories</label>
                    <div class="categories">
                        <button class="category-btn" data-category="Art">🎨 Art</button>
                        <button class="category-btn" data-category="Musique">🎵 Musique</button>
                        <button class="category-btn" data-category="Sport">⚽ Sport</button>
                        <button class="category-btn" data-category="Humanitaire">❤️ Humanitaire</button>
                        <button class="category-btn" data-category="Droits">⚖️ Droits / Inclusion</button>
                        <button class="category-btn" data-category="Enseignement">📚 Enseignement</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex justify-center items-center mt-20 w-3/4">
        <div class="flex gap-x-10 gap-y-5 flex-row flex-wrap w-2/3">
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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".association-link").forEach(link => {
                link.addEventListener("click", function (event) {
                    event.preventDefault(); // Empêche la redirection immédiate
                    let associationId = this.getAttribute("data-id");

                    // Envoi de l'ID en session via une requête AJAX
                    fetch("set_session.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "association_id=" + associationId
                    })
                        .then(response => response.text())
                        .then(() => {
                            window.location.href = "association.php"; // Redirection après stockage
                        });
                });
            });
        });
    </script>

    <script type="module" src="../node_modules/dropzone/dist/dropzone-min.js"></script>
    <script type="module" src="js/script.js"></script>
</body>

</html>