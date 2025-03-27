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
<style>
  /* Style pour la truncation du texte */
  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  
  .line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  
  /* Animation de hover améliorée */
  .card-container > a:hover, 
  .associations-container > a:hover {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  }
  
  /* Style pour les filtres en position sticky */
  @media (min-width: 768px) {
    .sticky {
      position: sticky;
      top: 2rem;
    }
  }
  
  /* Améliorations visuelles pour les boutons de pagination */
  .pagination button {
    transition: all 0.2s ease-in-out;
  }
  
  .pagination button:hover {
    transform: translateY(-1px);
  }
  /* Ajoutez ces styles à la section style existante */
  .bg-pattern {
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  }
  
  /* Customisation de l'input range */
  input[type="range"] {
    -webkit-appearance: none;
    height: 6px;
    background: #e5e7eb;
    border-radius: 5px;
  }
  
  input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #ec4899;
    cursor: pointer;
    border: 2px solid white;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  }
  
  input[type="range"]::-moz-range-thumb {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #ec4899;
    cursor: pointer;
    border: 2px solid white;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  }
  
  /* Scrollbar personnalisée */
  .scrollbar-thin::-webkit-scrollbar {
    width: 6px;
  }
  
  .scrollbar-thin::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
  }
  
  .scrollbar-thin::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 10px;
  }
  
  .scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
  }
</style>
<body>
    <!-- Remplacer le titre actuel par cette section -->
<!-- Remplacer l'en-tête actuel par celui-ci -->
<div class="bg-gradient-to-r from-purple-100 via-pink-50 to-rose-100 py-12 mb-10 rounded-xl mx-6 lg:mx-12 mt-8 shadow-lg relative overflow-hidden">
  <div class="absolute inset-0 bg-pattern opacity-5"></div>
  <div class="relative z-10">
    <h1 class="text-4xl md:text-5xl font-bold text-center text-gray-800 mb-3">Missions recommandées pour vous</h1>
    <p class="text-center text-gray-600 mt-3 max-w-2xl mx-auto px-4 text-lg">
      Découvrez des opportunités de bénévolat qui correspondent à vos centres d'intérêt et à votre localisation.
    </p>
    <div class="flex justify-center mt-6">
      <div class="bg-white/60 backdrop-blur-sm px-4 py-2 rounded-full text-sm text-gray-600 flex items-center gap-2 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-pink-500">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
        </svg>
        <?= htmlspecialchars($user_adress['name'] ?? 'Votre localisation') ?>
      </div>
    </div>
  </div>
</div>

    <!-- Remplacer tout le bloc main-container actuel par ce code -->
<div class="main-container flex flex-col md:flex-row px-4 lg:px-10">
  <div class="md:w-1/4 mb-6 md:mb-0">
  <div class="sticky top-24">
  <div class="filtres flex flex-col gap-5 p-6 bg-white rounded-xl shadow-lg w-full mt-6 border border-gray-100">
    <h2 class="text-xl font-bold text-gray-800 pb-3 border-b border-gray-200 flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-pink-500">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
      </svg>
      Filtres
    </h2>
    
    <div class="space-y-1.5">
      <label for="city" class="font-medium text-gray-700 text-sm flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
        </svg>
        Ville
      </label>
      <input type="text" id="city" placeholder="Rechercher une ville..." class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition-all">
    </div>

    <div class="space-y-1.5">
      <label for="distance" class="font-medium text-gray-700 text-sm flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
        </svg>
        Distance
      </label>
      <div class="slider-container flex flex-col gap-2">
        <input type="range" id="distance" min="1" max="200" value="10" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-pink-500" step="10">
        <div class="flex justify-between items-center">
          <span class="text-xs text-gray-500">1 km</span>
          <span id="distanceValue" class="px-2.5 py-1 bg-pink-100 text-pink-800 rounded-full text-xs font-medium">50 km</span>
          <span class="text-xs text-gray-500">200 km</span>
        </div>
      </div>
    </div>

    <div class="space-y-1.5">
      <label for="sort" class="font-medium text-gray-700 text-sm flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25" />
        </svg>
        Trier par
      </label>
      <select id="sort" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-200 focus:border-pink-400 outline-none transition-all bg-white">
        <option value="closest">Le plus proche</option>
        <option value="recent">Le plus récent</option>
        <option value="volunteers_needed">Places disponibles</option>
      </select>
    </div>

    <div class="space-y-2">
      <label class="font-medium text-gray-700 text-sm flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
        </svg>
        Tags
      </label>
      <div class="categories flex flex-wrap gap-2 max-h-40 overflow-y-auto pr-2 pb-2 scrollbar-thin">
      <div class="categories flex flex-wrap gap-2 max-h-40 overflow-y-auto pr-2 pb-2 scrollbar-thin">
  <?php
  // Récupérer tous les tags des missions
  $allTags = [];
  foreach ($missions as $mission) {
    if (isset($mission['tags']) && is_array($mission['tags'])) {
      foreach ($mission['tags'] as $tag) {
        if (!in_array($tag, $allTags)) {
          $allTags[] = $tag;
        }
      }
    }
  }

  // Trier alphabétiquement
  sort($allTags);

  // Afficher les boutons de tags
  foreach ($allTags as $tag) {
    echo '<button type="button" class="category-btn border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs px-2.5 py-1 rounded-full transition-colors" data-tag="' . htmlspecialchars($tag) . '">' . htmlspecialchars($tag) . '</button>';
  }

  // Si aucun tag
  if (empty($allTags)) {
    echo '<span class="text-sm text-gray-500">Aucune catégorie disponible</span>';
  }
  ?>
</div>
      </div>
    </div>
  </div>
</div>
  </div>
  
  <div class="md:w-3/4 pl-0 md:pl-6">
  <div class="missions-container">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-pink-500">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
      </svg>
      Missions près de chez vous
    </h2>
    <div class="flex items-center gap-2">
      <span class="text-gray-600 text-sm font-medium"><span id="mission-count" class="font-semibold text-pink-600"><?= count($missions) ?></span> missions trouvées</span>
    </div>
  </div>
  
  <div class="pagination flex gap-2 justify-end mb-4" id="mission-pagination">
    <!-- La pagination sera générée par JavaScript -->
  </div>
  
  <div class="card-container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
    <?php if (!empty($missions)): ?>
      <?php foreach ($missions as $mission): ?>
        <a class="transform bg-white rounded-lg shadow-md flex flex-col overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1 mission-link group"
           href="mission.php?id=<?= htmlspecialchars($mission['mission_id']) ?>" data-id="<?= htmlspecialchars($mission['mission_id']) ?>">
          <div class="relative overflow-hidden">
            <img src="<?= htmlspecialchars($mission['image_url'] ?: 'assets/uploads/background_image/defaultMission.jpg') ?>" alt="<?= htmlspecialchars($mission['title']) ?>"
                 class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-500">
            <div class="absolute top-0 right-0 bg-pink-500/90 text-white text-xs font-medium px-2.5 py-1 rounded-bl-lg">
              <?= round($mission['distance'], 1) ?> km
            </div>
          </div>
          <div class="p-5 flex flex-col h-full">
            <h2 class="text-lg font-bold text-gray-800 group-hover:text-pink-600 transition-colors"><?= htmlspecialchars($mission['title']) ?></h2>
            <p class="text-sm text-gray-500 mt-1 flex items-center gap-1">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
              </svg>
              <?= htmlspecialchars($mission['association_name']) ?>
            </p>

            <div class="flex flex-wrap gap-1 mt-3">
              <?php if (!empty($mission['tags']) && is_array($mission['tags'])): ?>
                <?php foreach (array_slice($mission['tags'], 0, 3) as $tag): ?>
                  <span class="bg-pink-100 text-pink-800 text-xs px-2 py-1 rounded-full"><?= htmlspecialchars($tag) ?></span>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>

            <p class="text-sm text-gray-700 mt-3 line-clamp-3 flex-grow">
              <?= htmlspecialchars($mission['description']) ?>
            </p>

            <div class="mt-auto pt-3 border-t border-gray-100 flex justify-between items-center">
              <span class="text-xs text-gray-500 flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                <?= date('d/m/Y', strtotime($mission['created_at'])) ?>
              </span>
              <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                </svg>
                <?= intval($mission['volunteers_registered']) ?>/<?= intval($mission['volunteers_needed']) ?>
              </span>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-span-3 py-16 flex flex-col items-center justify-center bg-gray-50 rounded-xl border border-gray-200">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-gray-300 mb-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
        </svg>
        <p class="text-gray-600 text-center mb-2">Aucune mission trouvée</p>
        <p class="text-gray-500 text-sm text-center max-w-md">Essayez de modifier vos critères de recherche ou d'élargir la zone géographique.</p>
      </div>
    <?php endif; ?>
  </div>
</div>
  </div>
</div>
    <!-- Ajouter cette section après la div "main-container" -->
    <div class="associations-recommendations px-4 lg:px-10 mb-16">
  <div class="bg-gradient-to-r from-indigo-100 via-blue-50 to-sky-100 py-8 px-8 rounded-xl mb-8 shadow-md relative overflow-hidden">
    <div class="absolute inset-0 bg-pattern opacity-5"></div>
    <div class="relative z-10">
      <h2 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-indigo-500">
          <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
        </svg>
        Associations recommandées
      </h2>
      <p class="text-gray-600 mt-2 text-lg">Découvrez des associations actives près de chez vous</p>
    </div>
  </div>
  <?php
// Récupérer les associations
$sql_assoc = "SELECT a.*, 
              (SELECT COUNT(*) FROM missions WHERE association_id = a.association_id) AS mission_count 
              FROM association a";
$result_assoc = $conn->query($sql_assoc);
$associations = [];

if ($result_assoc->num_rows > 0) {
  while($row = $result_assoc->fetch_assoc()) {
    if (!empty($row['association_adress'])) {
      $assoc_address = json_decode($row['association_adress'], true);
      if (isset($assoc_address['coordinates'])) {
        $assoc_lat = $assoc_address['coordinates'][1];
        $assoc_lon = $assoc_address['coordinates'][0];
        
        // Calculer la distance
        $distance = haversine($user_lat, $user_lon, $assoc_lat, $assoc_lon);
        $row['distance'] = $distance;
        
        $associations[] = $row;
      }
    }
  }
  
  // Trier par distance
  usort($associations, fn($a, $b) => $a['distance'] <=> $b['distance']);
}
?>
  <div class="associations-container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php
    // Garde le code PHP existant mais remplace le HTML généré par celui-ci
    if ($result_assoc->num_rows > 0) {
      // Traitement et tri des associations...
      
      // Afficher les 6 premières associations
      $displayed_assoc = array_slice($associations, 0, 6);
      foreach($displayed_assoc as $assoc) {
        $logo = !empty($assoc['association_background_image']) ? $assoc['association_background_image'] : 'assets/uploads/logo/default_logo.png';
        echo '
        <a href="association.php?id='.$assoc['association_id'].'" class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden group">
          <div class="h-36 relative overflow-hidden">
            <img src="'.$logo.'" alt="'.$assoc['association_name'].'" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            <div class="absolute bottom-3 left-3 right-3 text-white">
              <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold truncate flex-1">'.$assoc['association_name'].'</h3>
                <span class="text-sm bg-indigo-500/90 px-2 py-0.5 rounded-full ml-2 flex-shrink-0">'.round($assoc['distance'], 1).' km</span>
              </div>
            </div>
          </div>
          <div class="p-4 flex-grow">
            <div class="flex items-center text-sm text-gray-500 mb-2">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1 text-indigo-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.115 5.19l.319 1.913A6 6 0 008.11 10.36L9.75 12l-.387.775c-.217.433-.132.956.21 1.298l1.348 1.348c.21.21.329.497.329.795v1.089c0 .426.24.815.622 1.006l.153.076c.433.217.956.132 1.298-.21l.723-.723a8.7 8.7 0 002.288-4.042 1.087 1.087 0 00-.358-1.099l-1.33-1.108c-.251-.21-.582-.299-.905-.245l-1.17.195a1.125 1.125 0 01-.98-.314l-.295-.295a1.125 1.125 0 010-1.591l.13-.132a1.125 1.125 0 011.3-.21l.603.302a.809.809 0 001.086-1.086L14.25 7.5l1.256-.837a4.5 4.5 0 001.528-1.732l.146-.292M6.115 5.19A9 9 0 1017.18 4.64M6.115 5.19A8.965 8.965 0 0112 3c1.929 0 3.716.607 5.18 1.64" />
              </svg>
              '.$assoc['mission_count'].' mission'.($assoc['mission_count'] > 1 ? 's' : '').'
            </div>
            <p class="text-sm text-gray-700 line-clamp-2 mb-3">'.
              (isset($assoc['association_description']) ? htmlspecialchars(substr($assoc['association_description'], 0, 120)).'...' : 'Aucune description disponible')
            .'</p>
            <div class="flex justify-between items-center">
              <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">Association</span>
              <span class="text-indigo-600 group-hover:text-indigo-800 transition-colors text-sm font-medium flex items-center">
                Voir le profil
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
              </span>
            </div>
          </div>
        </a>';
      }
    } else {
      echo `<div class="col-span-3 py-16 flex flex-col items-center justify-center bg-gray-50 rounded-xl border border-gray-200">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-gray-300 mb-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
        </svg>
        <p class="text-gray-600 text-center mb-2">Aucune association trouvée dans votre région</p>
        <p class="text-gray-500 text-sm text-center max-w-md">Essayez d'élargir votre zone de recherche pour découvrir plus d'associations.</p>
      </div>`;
    }
    ?>
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




        // Ajouter avant la fermeture de la balise script

// Pagination pour les missions
function setupPagination() {
  const missionsPerPage = 9;
  const missionCards = document.querySelectorAll('.card-container > a');
  const paginationContainer = document.getElementById('mission-pagination');
  const missionCount = document.getElementById('mission-count');
  
  // Si moins de missionsPerPage, pas besoin de pagination
  if (missionCards.length <= missionsPerPage) {
    paginationContainer.innerHTML = '';
    return;
  }
  
  const pageCount = Math.ceil(missionCards.length / missionsPerPage);
  let currentPage = 1;
  
  // Créer les boutons de pagination
  paginationContainer.innerHTML = '';
  for (let i = 1; i <= pageCount; i++) {
    const pageBtn = document.createElement('button');
    pageBtn.textContent = i;
    pageBtn.className = 'px-3 py-1 rounded ' + (i === currentPage ? 'bg-pink-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300');
    pageBtn.addEventListener('click', () => goToPage(i));
    paginationContainer.appendChild(pageBtn);
  }
  
  // Afficher la page actuelle
  function goToPage(page) {
    currentPage = page;
    
    // Mettre à jour l'affichage des missions
    missionCards.forEach((card, index) => {
      const shouldShow = index >= (page - 1) * missionsPerPage && index < page * missionsPerPage;
      card.style.display = shouldShow ? '' : 'none';
    });
    
    // Mettre à jour les boutons de pagination
    const buttons = paginationContainer.querySelectorAll('button');
    buttons.forEach((btn, i) => {
      btn.className = 'px-3 py-1 rounded ' + ((i + 1) === page ? 'bg-pink-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300');
    });
  }
  
  // Initialiser à la première page
  goToPage(1);
}

// Appeler setupPagination après le chargement initial et après chaque mise à jour des missions
document.addEventListener("DOMContentLoaded", function() {
  setupPagination();
  
  // Mettre à jour la fonction updateMissionsUI pour réinitialiser la pagination
  const originalUpdateMissionsUI = updateMissionsUI;
  updateMissionsUI = function(data) {
    originalUpdateMissionsUI(data);
    setupPagination();
  };
});
    </script>
</body>

</html>