<?php
session_start();
include('../backend/db.php');

// Vérification du type d'utilisateur (association)
if (!isset($_SESSION['user_id']) || $_SESSION['type'] != 1) {
    header('Location: login.php');
    exit();
}

// Récupération des missions de l'association
$query = "SELECT * FROM missions WHERE association_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$missions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Association - HeartHive</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #CF3275;
            --primary-light: #FFE9EF;
            --primary-hover: #ffd6e2;
            --text-primary: #374151;
        }

        /* Style général */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F9FAFB;
        }

        .page-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Cards des statistiques */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: var(--primary);
        }

        /* Boutons */
        .btn-primary {
            background: var(--primary-light);
            border: 2px solid var(--primary);
            color: var(--primary);
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            transition: all 0.2s ease;
            box-shadow: none !important;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            box-shadow: 0 4px 12px rgba(207, 50, 117, 0.15);
            transform: translateY(-1px);
        }

        /* Tags */
        .tag {
            display: inline-flex;
            align-items: center;
            background-image: linear-gradient(to right, #fdf5f9, #ffe9ef);
            color: var(--primary);
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            margin: 0.25rem;
            border: 1px solid #FFCAD8;
            box-shadow: 0 2px 4px rgba(207, 50, 117, 0.1);
            transition: all 0.2s ease;
        }

        .tag:hover {
            background-image: linear-gradient(to right, #ffe9ef, #ffd6e2);
            transform: translateY(-1px);
            box-shadow: 0 3px 6px rgba(207, 50, 117, 0.15);
        }

        /* Formulaires et inputs */
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(207, 50, 117, 0.1);
            outline: none;
        }

        /* Modal */
        .modal-content {
            background: white;
            border-radius: 12px;
            box-shadow: none;

            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Upload zone */
        .upload-zone {
            border: 2px dashed #FFCAD8;
            padding: 2rem;
            text-align: center;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .upload-zone:hover {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .grid-cols-3 {
                grid-template-columns: 1fr;
            }

            .stat-card {
                margin-bottom: 1rem;
            }
        }

        /* Modifier le style du conteneur principal pour la superposition */
        .container {
            position: relative;
            z-index: 1;
        }

        /* Ajuster le style du header */
        header {
            position: fixed !important;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 10;
        }

        /* Ajuster le modal pour qu'il soit au-dessus de tout */
        #newMissionModal {
            z-index: 50;
        }

        /* Ajuster l'espacement du contenu principal */
        .container.mx-auto {
            margin-top: 5rem;
        }

        /* Modifier le style de la table */
        .bg-white.rounded-lg {
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Style amélioré pour l'upload d'image */
        .upload-container {
            border: 2px dashed #FFB3E4;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .upload-container:hover {
            border-color: #CF3275;
            background-color: #FFF5F9;
        }

        .image-preview {
            max-width: 100%;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }

        .image-preview img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        /* Cacher le script qui s'affiche */
        script {
            display: none !important;
        }

        /* Style pour l'autocomplétion */
        #suggestions {
            position: absolute;
            width: 100%;
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 0.5rem;
            margin-top: 0.25rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            /* Augmenter le z-index */
            max-height: 200px;
            overflow-y: auto;
        }

        .suggestion {
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-bottom: 1px solid #F3F4F6;
        }

        .suggestion:before {
            content: '\f3c5';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: var(--primary);
            font-size: 0.875rem;
        }

        .suggestion:hover {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .suggestion:last-child {
            border-bottom: none;
        }

        /* Conteneur de recherche */
        .search-container {
            position: relative;
            width: 100%;
        }

        #city-search {
            padding-left: 2.5rem;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>');
            background-repeat: no-repeat;
            background-position: 0.75rem center;
            background-size: 1.25rem;
        }

        #suggestions:not(.hidden) {
            display: block;
        }

        /* Modifier le style de la carte */
        #map {
            height: 300px;
            width: 100%;
            border-radius: 0.5rem;
            z-index: 1;
            border: 1px solid #E5E7EB;
            position: relative;
            display: block;
            min-height: 300px;
            margin: 0 auto;
            overflow: hidden;
        }

        .leaflet-container {
            width: 100% !important;
            height: 100% !important;
            border-radius: 0.5rem;
        }

        .leaflet-control-zoom {
            margin-top: 10px !important;
            margin-left: 10px !important;
        }

        /* Ajouter dans la section des styles */
        .cropper-container {
            max-height: 60vh;
        }

        .cropper-view-box {
            outline: 1px solid #fff;
            outline-color: rgba(255, 255, 255, 0.75);
        }

        .cropper-point {
            background-color: #CF3275;
        }

        .cropper-line {
            background-color: #fff;
        }

        /* Animation pour le modal de recadrage */
        #crop-modal {
            transition: opacity 0.3s ease;
        }

        #crop-modal.fade-in {
            opacity: 1;
        }

        #crop-modal.fade-out {
            opacity: 0;
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
</head>

<body class="bg-gray-50">
    <?php include('include/header.php'); ?>

    <div class="container mx-auto px-4 py-8 mt-20 relative z-1">
        <!-- En-tête avec stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stat-card group">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Missions actives</h3>
                        <p class="text-3xl font-bold text-pink-600 mt-2"><?php echo count($missions); ?></p>
                    </div>
                    <div class="text-pink-500 text-3xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-tasks"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Bénévoles inscrits</h3>
                        <p class="text-3xl font-bold text-pink-600 mt-2">0</p>
                    </div>
                    <div class="text-pink-500 text-3xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Taux de participation</h3>
                        <p class="text-3xl font-bold text-pink-600 mt-2">0%</p>
                    </div>
                    <div class="text-pink-500 text-3xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- En-tête des missions -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Gestion des missions</h1>
            <button onclick="openModal('newMissionModal')"
                class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Nouvelle Mission
            </button>
        </div>

        <!-- Liste des missions -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <?php if (empty($missions)): ?>
                <div class="p-6 text-center text-gray-500">
                    <p>Aucune mission n'a encore été créée</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mission</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lieu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bénévoles</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tags</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($missions as $mission): ?>
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <?php if ($mission['image_url']): ?>
                                                <img src="<?php echo htmlspecialchars($mission['image_url']); ?>"
                                                    class="h-10 w-10 rounded-full mr-3 object-cover">
                                            <?php endif; ?>
                                            <div class="font-medium text-gray-900">
                                                <a href="page_mission.php?id=<?php echo $mission['mission_id']; ?>" class="hover:text-pink-600 transition-colors">
                                                    <?php echo htmlspecialchars($mission['title']); ?>
                                                </a>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo substr(htmlspecialchars($mission['description']), 0, 50) . '...'; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?php
                                        $location = json_decode($mission['location'], true);
                                        echo htmlspecialchars($location['name'] ?? 'Non défini');
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?php echo $mission['volunteers_registered']; ?>/<?php echo $mission['volunteers_needed']; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php
                                        $tags = json_decode($mission['tags'], true);
                                        if ($tags):
                                            foreach ($tags as $tag): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800 mr-1">
                                                    <?php echo htmlspecialchars($tag); ?>
                                                </span>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <div class="flex space-x-3">
                                            <button onclick="openModal('editMissionModal', <?php echo $mission['mission_id']; ?>)"
                                                class="text-indigo-600 hover:text-indigo-900">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="confirmDelete(<?php echo $mission['mission_id']; ?>)"
                                                class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Nouvelle Mission -->
    <div id="newMissionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
            <div class="modal-content">
                <div class="flex justify-between items-center mb-6 border-b pb-4">
                    <h3 class="text-2xl font-semibold text-gray-800">Nouvelle Mission</h3>
                    <button onclick="closeModal('newMissionModal')"
                        class="text-gray-400 hover:text-gray-500 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="newMissionForm" action="../backend/create_mission.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <!-- Titre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Titre de la mission</label>
                        <input type="text"
                            name="title"
                            required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500">
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description"
                            rows="4"
                            required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500"></textarea>
                    </div>

                    <!-- Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Image de la mission</label>
                        <div id="upload-container" class="upload-container mt-1">
                            <input type="file" id="image-input" name="image" accept="image/*" class="hidden">
                            <div id="upload-content" class="space-y-2">
                                <i class="fas fa-cloud-upload-alt text-3xl text-pink-500"></i>
                                <p class="text-sm text-gray-600">Cliquez ou glissez une image ici</p>
                                <p class="text-xs text-gray-500">PNG, JPG jusqu'à 5MB</p>
                            </div>
                            <div id="preview-container" class="hidden"></div>
                        </div>
                        <!-- Ajout pour le recadrage -->
                        <input type="hidden" name="cropped_image" id="cropped-data">
                    </div>

                    <!-- Localisation -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ville</label>
                        <div class="search-container">
                            <input type="text"
                                id="city-search"
                                placeholder="Rechercher une ville..."
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            <div id="suggestions" class="hidden"></div>
                        </div>
                        <input type="hidden" id="location-data" name="location_data">
                    </div>

                    <!-- Carte -->
                    <div id="map" class="h-[200px] w-full rounded-md shadow-sm"></div>

                    <!-- Nombre de bénévoles -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre de bénévoles requis</label>
                        <input type="number"
                            name="volunteers_needed"
                            min="1"
                            value="1"
                            required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500">
                    </div>

                    <!-- Compétences -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Compétences requises</label>
                        <input type="text"
                            name="skills"
                            placeholder="Ex: permis B, secourisme (séparées par des virgules)"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500">
                    </div>

                    <!-- Tags -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tags</label>
                        <input type="text"
                            name="tags"
                            placeholder="Ex: social, environnement (séparés par des virgules)"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500">
                    </div>

                    <!-- Boutons -->
                    <div class="flex justify-end gap-2 pt-4 border-t"></div>
                    <button type="button"
                        onclick="closeModal('newMissionModal')"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Annuler
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-pink-600 text-white rounded-md hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                        Créer la mission
                    </button>
            </div>
            </form>
        </div>
    </div>
    </div>

    <!-- Ajouter ce div pour le modal de recadrage -->
    <div id="crop-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 hidden z-[60] flex justify-center items-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-3xl mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Recadrer l'image</h3>
                <button id="close-crop" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="relative">
                <img id="image-to-crop" src="" class="max-h-[60vh] max-w-full mx-auto block">
            </div>
            <div class="flex justify-end mt-4 space-x-2">
                <button id="cancel-crop" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    Annuler
                </button>
                <button id="apply-crop" class="px-4 py-2 bg-pink-600 text-white rounded hover:bg-pink-700">
                    Appliquer
                </button>
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        let currentMap = null;
        let cityMarker = null;

        // Fonctions de base pour les modals
        function openModal(modalId, missionId = null) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('hidden');

            // Attendre que le modal soit visible avant d'initialiser la carte
            setTimeout(initializeMap, 300);

            if (missionId) {
                console.log('Édition de la mission:', missionId);
            }
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            if (currentMap) {
                currentMap.remove();
                currentMap = null;
            }
        }

        function confirmDelete(missionId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette mission ?')) {
                window.location.href = `../backend/delete_mission.php?id=${missionId}`;
            }
        }

        // Initialisation de la carte
        function initializeMap() {
            if (currentMap) {
                currentMap.remove();
            }

            const mapElement = document.getElementById('map');
            if (!mapElement) return;

            currentMap = L.map('map', {
                zoomControl: true,
                scrollWheelZoom: true
            }).setView([46.603354, 1.888334], 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(currentMap);

            // Forcer l'invalidation pour corriger l'affichage
            setTimeout(() => {
                currentMap.invalidateSize();
            }, 100);
        }

        // Gestion des suggestions de villes
        function setupCitySearch() {
            const citySearch = document.getElementById('city-search');
            const suggestions = document.getElementById('suggestions');
            const locationData = document.getElementById('location-data');

            if (!citySearch || !suggestions || !locationData) return;

            // Gestionnaire d'événements pour l'input de recherche
            citySearch.addEventListener('input', async () => {
                const searchTerm = citySearch.value.trim();

                if (searchTerm.length > 2) {
                    try {
                        const response = await fetch(`https://geo.api.gouv.fr/communes?nom=${searchTerm}&fields=nom,centre,code&boost=population&limit=5`);
                        const data = await response.json();
                        showSuggestions(data);
                    } catch (error) {
                        console.error("Erreur:", error);
                        hideSuggestions();
                    }
                } else {
                    hideSuggestions();
                }
            });

            // Fonction pour afficher les suggestions
            function showSuggestions(communes) {
                if (communes.length === 0) {
                    suggestions.innerHTML = `
                    <div class="suggestion text-gray-500">
                        Aucune ville trouvée
                    </div>
                `;
                } else {
                    suggestions.innerHTML = communes.map(commune => `
                    <div class="suggestion" 
                        data-code="${commune.code}" 
                        data-lat="${commune.centre.coordinates[1]}" 
                        data-lon="${commune.centre.coordinates[0]}">
                        ${commune.nom}
                    </div>
                `).join('');
                }

                suggestions.classList.remove('hidden');
                suggestions.style.display = 'block';
            }

            // Fonction pour cacher les suggestions
            function hideSuggestions() {
                suggestions.classList.add('hidden');
                suggestions.style.display = 'none';
            }

            // Gestion du clic sur une suggestion
            suggestions.addEventListener('click', function(e) {
                const suggestion = e.target.closest('.suggestion');
                if (!suggestion) return;

                const lat = parseFloat(suggestion.dataset.lat);
                const lon = parseFloat(suggestion.dataset.lon);
                const name = suggestion.textContent.trim();

                // Mettre à jour la carte si elle existe
                if (currentMap) {
                    // Supprimer l'ancien marqueur s'il existe
                    if (cityMarker) {
                        currentMap.removeLayer(cityMarker);
                    }

                    // Ajouter le nouveau marqueur
                    cityMarker = L.marker([lat, lon])
                        .addTo(currentMap)
                        .bindPopup(name)
                        .openPopup();

                    // Centrer la carte sur le marqueur
                    currentMap.setView([lat, lon], 12);
                }

                // Mettre à jour les champs du formulaire
                citySearch.value = name;
                locationData.value = JSON.stringify({
                    name: name,
                    coordinates: [lon, lat]
                });

                // Cacher les suggestions
                hideSuggestions();
            });

            // Fermer les suggestions en cliquant ailleurs
            document.addEventListener('click', (e) => {
                if (!e.target.closest('#city-search') && !e.target.closest('#suggestions')) {
                    hideSuggestions();
                }
            });
        }

        // Gestion de l'upload d'image avec recadrage
        function setupImageUpload() {
            const uploadContainer = document.getElementById('upload-container');
            const imageInput = document.getElementById('image-input');
            const previewContainer = document.getElementById('preview-container');
            const uploadContent = document.getElementById('upload-content');
            const cropModal = document.getElementById('crop-modal');
            const imageToCrop = document.getElementById('image-to-crop');
            const applyCropBtn = document.getElementById('apply-crop');
            const cancelCropBtn = document.getElementById('cancel-crop');
            const closeCropBtn = document.getElementById('close-crop');
            const croppedDataInput = document.getElementById('cropped-data');

            let cropper = null;
            let originalFile = null;

            if (!uploadContainer || !imageInput || !previewContainer || !uploadContent) return;

            uploadContainer.addEventListener('click', () => imageInput.click());

            uploadContainer.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadContainer.classList.add('border-pink-500', 'bg-pink-50');
            });

            uploadContainer.addEventListener('dragleave', () => {
                uploadContainer.classList.remove('border-pink-500', 'bg-pink-50');
            });

            uploadContainer.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadContainer.classList.remove('border-pink-500', 'bg-pink-50');

                const file = e.dataTransfer.files[0];
                if (file && file.type.startsWith('image/')) {
                    handleImageUpload(file);
                }
            });

            imageInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    handleImageUpload(file);
                }
            });

            function handleImageUpload(file) {
                originalFile = file;
                const reader = new FileReader();
                reader.onload = (e) => {
                    imageToCrop.src = e.target.result;
                    cropModal.classList.remove('hidden');

                    // Initialiser cropper après un court délai
                    setTimeout(() => {
                        if (cropper) {
                            cropper.destroy();
                        }
                        cropper = new Cropper(imageToCrop, {
                            aspectRatio: 16 / 9,
                            viewMode: 1,
                            dragMode: 'move',
                            autoCropArea: 0.8,
                            restore: false,
                            guides: true,
                            center: true,
                            highlight: false,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                            toggleDragModeOnDblclick: false,
                        });
                    }, 100);
                };
                reader.readAsDataURL(file);
            }

            // Gérer le bouton d'application du recadrage
            applyCropBtn.addEventListener('click', () => {
                if (!cropper) return;

                const canvas = cropper.getCroppedCanvas({
                    width: 800, // Largeur maximale
                    height: 450, // Hauteur maximale (ratio 16:9)
                    fillColor: '#fff'
                });

                const croppedImageUrl = canvas.toDataURL('image/jpeg', 0.9);
                croppedDataInput.value = croppedImageUrl;

                // Afficher l'aperçu
                uploadContent.classList.add('hidden');
                previewContainer.classList.remove('hidden');
                previewContainer.innerHTML = `
                    <div class="image-preview">
                        <img src="${croppedImageUrl}" alt="Aperçu">
                        <button type="button" onclick="removeUploadedImage()" 
                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-2 hover:bg-red-600 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;

                // Fermer le modal
                cropModal.classList.add('hidden');
                cropper.destroy();
                cropper = null;
            });

            // Gérer l'annulation ou la fermeture
            cancelCropBtn.addEventListener('click', closeImageCrop);
            closeCropBtn.addEventListener('click', closeImageCrop);

            function closeImageCrop() {
                cropModal.classList.add('hidden');
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
            }
        }

        // Modifier la fonction removeUploadedImage pour prendre en compte le recadrage
        function removeUploadedImage() {
            const imageInput = document.getElementById('image-input');
            const previewContainer = document.getElementById('preview-container');
            const uploadContent = document.getElementById('upload-content');
            const croppedDataInput = document.getElementById('cropped-data');

            if (imageInput) imageInput.value = '';
            if (croppedDataInput) croppedDataInput.value = '';
            if (previewContainer) {
                previewContainer.classList.add('hidden');
                previewContainer.innerHTML = '';
            }
            if (uploadContent) uploadContent.classList.remove('hidden');
        }

        // Animation des stats
        function animateStats() {
            const stats = document.querySelectorAll('.stat-card p');
            stats.forEach(stat => {
                const value = parseInt(stat.textContent);
                if (!isNaN(value)) {
                    let current = 0;
                    const increment = value / 30;
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= value) {
                            clearInterval(timer);
                            current = value;
                        }
                        stat.textContent = Math.round(current);
                    }, 30);
                }
            });
        }

        // Initialisation au chargement du document
        document.addEventListener('DOMContentLoaded', function() {
            // Exposer la fonction removeUploadedImage au niveau global
            window.removeUploadedImage = removeUploadedImage;

            // Initialiser tous les composants
            setupCitySearch();
            setupImageUpload();
            animateStats();

            // Validation du formulaire
            const form = document.getElementById('newMissionForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const title = this.querySelector('[name="title"]').value.trim();
                    const description = this.querySelector('[name="description"]').value.trim();
                    const locationData = document.getElementById('location-data').value;

                    if (!title || !description) {
                        alert('Veuillez remplir tous les champs requis');
                        return;
                    }

                    if (!locationData) {
                        alert('Veuillez sélectionner une ville pour la mission');
                        return;
                    }

                    this.submit();
                });
            }

            // Fermer les modals avec Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.fixed').forEach(modal => {
                        if (!modal.classList.contains('hidden')) {
                            modal.classList.add('hidden');
                            if (currentMap) {
                                currentMap.remove();
                                currentMap = null;
                            }
                        }
                    });
                }
            });
        });
    </script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
</body>

</html>