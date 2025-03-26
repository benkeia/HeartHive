<?php
session_start();
require_once '../backend/db.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion
    header('Location: login.php');
    exit;
}

// Récupération de l'ID de la mission depuis l'URL
$mission_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($mission_id === 0) {
    // Rediriger vers la page d'accueil si aucun ID n'est fourni
    header('Location: index.php');
    exit;
}

// Inclure le header
include './include/header.php';

// Vérification de l'existence de la mission - en utilisant les champs qui existent réellement
$stmt = $conn->prepare("SELECT 
                        m.mission_id, 
                        m.title, 
                        m.description, 
                        m.image_url, 
                        m.availability, 
                        m.location, 
                        m.skills_required, 
                        m.tags,
                        m.volunteers_needed, 
                        m.volunteers_registered,
                        m.created_at,
                        m.updated_at,
                        m.association_id,
                        a.association_name, 
                        a.association_profile_picture
                        FROM missions m 
                        JOIN association a ON m.association_id = a.association_id 
                        WHERE m.mission_id = ?");
$stmt->bind_param("i", $mission_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Rediriger vers la page d'accueil si la mission n'existe pas
    echo '<script>window.location.href = "index.php";</script>';
    exit;
}

$mission = $result->fetch_assoc();

// Valeurs par défaut pour les champs qui pourraient manquer
if (!isset($mission['volunteers_needed'])) $mission['volunteers_needed'] = 0;
if (!isset($mission['volunteers_registered'])) $mission['volunteers_registered'] = 0;
if (!isset($mission['mission_type'])) $mission['mission_type'] = '';
if (!isset($mission['frequency'])) $mission['frequency'] = '';

// Vérifier si l'utilisateur a déjà postulé à cette mission
$has_applied = false;
$application_status = null;

$check_table_query = "SHOW TABLES LIKE 'applications'";
$tables_result = $conn->query($check_table_query);
$applications_table_exists = ($tables_result->num_rows > 0);

if ($applications_table_exists) {
    // Si la table existe, vérifier si l'utilisateur a postulé
    $stmt = $conn->prepare("SELECT status FROM applications WHERE mission_id = ? AND volunteer_id = ?");
    $stmt->bind_param("ii", $mission_id, $_SESSION['user_id']);
    $stmt->execute();
    $application_result = $stmt->get_result();

    if ($application_result->num_rows > 0) {
        $has_applied = true;
        $application_row = $application_result->fetch_assoc();
        $application_status = $application_row['status'];
    }
}

// Préparation des données pour l'affichage
$location = json_decode($mission['location'], true);
$location_name = isset($location['name']) ? $location['name'] : 'Non précisé';
$coordinates = isset($location['coordinates']) ? $location['coordinates'] : [2.333333, 48.866667]; // Paris par défaut

$skills = json_decode($mission['skills_required'], true) ?: [];
$tags = json_decode($mission['tags'], true) ?: [];

// Calcul des places restantes
$places_remaining = $mission['volunteers_needed'] - ($mission['volunteers_registered'] ?? 0);

// Utiliser la date de création au lieu de start_date
$mission_date = date('d/m/Y', strtotime($mission['created_at']));
$availability = $mission['availability'] ? json_decode($mission['availability'], true) : null;

// Traduction des statuts pour affichage
$status_labels = [
    'pending' => 'En attente',
    'accepted' => 'Accepté',
    'rejected' => 'Refusé'
];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($mission['title']); ?> - HeartHive</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.js"></script>
    <style>
        .leaflet-container {
            z-index: 1 !important;
            /* Valeur plus basse que la modal */
        }

        #apply-modal {
            z-index: 999 !important;
            /* Valeur plus élevée que la carte */
        }

        #mission-map {
            height: 250px;
            width: 100%;
            border-radius: 8px;
        }

        .application-status-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .application-status-accepted {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .application-status-rejected {
            background-color: #FEE2E2;
            color: #B91C1C;
        }

        .tag {
            background-color: #F3F4F6;
            color: #4B5563;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .tag.skill {
            background-color: #EFF6FF;
            color: #1E40AF;
        }

        .mission-banner {
            position: relative;
            width: 100%;
            padding-top: 40%;
            /* Ratio réduit pour moins de hauteur */
            max-height: 300px;
            /* Hauteur maximale */
            overflow: hidden;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .mission-banner-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .mission-banner-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(0deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0) 100%);
            padding: 1.5rem;
            color: white;
        }

        .mission-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .mission-location {
            font-size: 1rem;
            opacity: 0.9;
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .mission-location i {
            margin-right: 0.5rem;
        }

        .mission-tags {
            display: flex;
            flex-wrap: wrap;
        }

        .mission-tags .tag {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        /* Remplacez votre règle CSS actuelle par celle-ci */

        /* Force toutes les couches de Leaflet à rester en-dessous de la modal */
        .leaflet-pane,
        .leaflet-top,
        .leaflet-bottom,
        .leaflet-control {
            z-index: 200 !important;
            /* Valeur plus basse que celle de la modal */
        }

        #apply-modal {
            z-index: 1000 !important;
            /* Valeur bien plus élevée que les éléments Leaflet */
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8 mt-20">
        <!-- Fil d'Ariane -->
        <div class="text-sm text-gray-500 mb-6">
            <a href="index.php" class="hover:text-pink-600">Accueil</a> &gt;
            <a href="missions.php" class="hover:text-pink-600">Missions</a> &gt;
            <span class="text-gray-700"><?php echo htmlspecialchars($mission['title']); ?></span>
        </div>

        <!-- Contenu principal avec layout 2/3 - 1/3 -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale (2/3) - à gauche -->
            <div class="lg:col-span-2 order-2 lg:order-1">
                <!-- Bannière de la mission -->
                <div class="mission-banner">
                    <img src="<?php echo !empty($mission['image_url']) ? htmlspecialchars($mission['image_url']) : 'assets/img/default-mission.jpg'; ?>"
                        alt="Image de la mission" class="mission-banner-image">

                    <div class="mission-banner-overlay">
                        <h1 class="mission-title"><?php echo htmlspecialchars($mission['title']); ?></h1>
                        <div class="mission-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo htmlspecialchars($location_name); ?></span>
                        </div>
                        <div class="mission-tags">
                            <?php if (is_array($tags) && count($tags) > 0): ?>
                                <?php foreach ($tags as $tag): ?>
                                    <span class="tag"><?php echo htmlspecialchars($tag); ?></span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Description de la mission -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center">
                            <img src="<?php echo isset($mission['association_profile_picture']) ? htmlspecialchars($mission['association_profile_picture']) : 'assets/img/default_asso.png'; ?>"
                                alt="Logo" class="w-12 h-12 rounded-full object-cover mr-4">
                            <div>
                                <h3 class="font-semibold text-gray-900">
                                    <?php echo isset($mission['association_name']) ? htmlspecialchars($mission['association_name']) : 'Non précisé'; ?>
                                </h3>
                                <p class="text-sm text-gray-500">
                                    Publié le <?php echo $mission_date; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <h2 class="text-xl font-semibold text-gray-900 mb-4">À propos de la mission</h2>
                    <div class="prose max-w-none">
                        <?php echo nl2br(htmlspecialchars($mission['description'])); ?>
                    </div>
                </div>

                <!-- Compétences requises -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Compétences recherchées</h2>
                    <?php if (is_array($skills) && count($skills) > 0): ?>
                        <div class="flex flex-wrap">
                            <?php foreach ($skills as $skill): ?>
                                <span class="tag skill"><?php echo htmlspecialchars($skill); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500">Aucune compétence spécifique n'est requise pour cette mission.</p>
                    <?php endif; ?>
                </div>

                <!-- Disponibilités -->
                <?php if ($availability): ?>
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Disponibilités requises</h2>
                        <ul class="list-disc pl-5">
                            <?php foreach ($availability as $day => $slots): ?>
                                <?php if (!empty($slots)): ?>
                                    <li class="mb-2">
                                        <strong><?php echo htmlspecialchars(ucfirst($day)); ?> :</strong>
                                        <?php
                                        $formatted_slots = array_map(function ($slot) {
                                            return $slot . 'h';
                                        }, $slots);
                                        echo htmlspecialchars(implode(', ', $formatted_slots));
                                        ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Informations pratiques -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations pratiques</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Date de création</h3>
                            <p class="font-medium text-gray-900">
                                <?php echo date('d/m/Y', strtotime($mission['created_at'])); ?>
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Places disponibles</h3>
                            <p class="font-medium <?php echo $places_remaining > 0 ? 'text-green-600' : 'text-red-600'; ?>">
                                <?php echo $places_remaining; ?> / <?php echo $mission['volunteers_needed']; ?>
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Dernière mise à jour</h3>
                            <p class="font-medium text-gray-900">
                                <?php echo date('d/m/Y', strtotime($mission['updated_at'])); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne latérale (1/3) - à droite -->
            <div class="order-1 lg:order-2">
                <!-- Bouton d'action comme un bloc indépendant sans div englobante -->
                <?php if ($has_applied): ?>
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <div class="application-status-<?php echo $application_status; ?> px-4 py-3 rounded-md text-center text-sm font-medium">
                            <span class="block mt-1 text-lg">
                                <?php echo $status_labels[$application_status] ?? 'En cours de traitement'; ?>
                            </span>
                        </div>
                    </div>
                <?php else: ?>
                    <button id="apply-button" class="block w-full bg-pink-600 hover:bg-pink-700 text-white font-medium px-6 py-4 rounded-md shadow-md mb-6 text-lg">
                        Postuler à cette mission
                    </button>
                <?php endif; ?>

                <!-- Informations sur l'association -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">À propos de l'association</h2>
                    <div class="flex items-center mb-4">
                        <img src="<?php echo isset($mission['association_profile_picture']) ? htmlspecialchars($mission['association_profile_picture']) : 'assets/img/default_asso.png'; ?>"
                            alt="Logo" class="w-16 h-16 rounded-full object-cover mr-4">
                        <div>
                            <h3 class="font-semibold text-gray-900">
                                <?php echo isset($mission['association_name']) ? htmlspecialchars($mission['association_name']) : 'Non précisé'; ?>
                            </h3>
                        </div>
                    </div>
                    <?php if (isset($mission['association_id'])): ?>
                        <a href="association.php?id=<?php echo $mission['association_id']; ?>"
                            class="block text-center w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-md transition-colors">
                            Voir le profil
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Carte de localisation -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Lieu de la mission</h2>
                    <div id="mission-map" class="mb-3"></div>

                    <!-- Adresse complète -->
                    <div class="text-sm text-gray-700">
                        <?php
                        $address_parts = [];
                        if (isset($location['street'])) $address_parts[] = htmlspecialchars($location['street']);
                        if (isset($location['number'])) $address_parts[] = htmlspecialchars($location['number']);
                        if (isset($location['postalCode'])) $address_parts[] = htmlspecialchars($location['postalCode']);
                        if (isset($location['name'])) $address_parts[] = htmlspecialchars($location['name']);

                        echo !empty($address_parts) ? implode(', ', $address_parts) : 'Adresse non précisée';
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de candidature -->
    <div id="apply-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-900">Postuler à la mission</h3>
                    <button id="close-modal" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <form id="apply-form">
                <div class="p-6">
                    <div class="mb-4">
                        <label for="motivation" class="block text-sm font-medium text-gray-700 mb-2">Votre motivation</label>
                        <textarea id="motivation" name="motivation" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500" placeholder="Expliquez pourquoi vous souhaitez participer à cette mission..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="availability" class="block text-sm font-medium text-gray-700 mb-2">Vos disponibilités</label>
                        <textarea id="availability" name="availability" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500" placeholder="Précisez vos disponibilités pour cette mission..."></textarea>
                    </div>
                    <input type="hidden" name="mission_id" value="<?php echo $mission_id; ?>">
                    <input type="hidden" name="volunteer_id" value="<?php echo $_SESSION['user_id']; ?>">
                </div>
                <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end">
                    <button type="button" id="cancel-apply" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 mr-2">
                        Annuler
                    </button>
                    <button type="submit" class="bg-pink-600 hover:bg-pink-700 py-2 px-4 rounded-md shadow-sm text-sm font-medium text-white">
                        Envoyer ma candidature
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Initialisation de la carte
        let map; // Déclaration de la variable au niveau global

        document.addEventListener('DOMContentLoaded', function() {
            try {
                // Coordonnées de l'emplacement de la mission - avec vérification
                let coordinates = <?php echo json_encode($coordinates); ?>;

                // S'assurer que les coordonnées sont valides - sinon utiliser Paris
                if (!Array.isArray(coordinates) || coordinates.length !== 2) {
                    coordinates = [48.866667, 2.333333]; // Paris par défaut (latitude, longitude)
                } else {
                    // Inverser les coordonnées si elles sont dans l'ordre [longitude, latitude]
                    coordinates = [coordinates[1], coordinates[0]];
                }

                // Créer la carte
                map = L.map('mission-map').setView(coordinates, 13); // Sans 'const'

                // Ajouter la couche de tuiles OpenStreetMap
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                // Ajouter un marqueur à l'emplacement de la mission
                L.marker(coordinates).addTo(map);

                // Essayer d'obtenir une vue d'ensemble de la ville
                const cityName = <?php echo json_encode($location_name ?? ''); ?>;
                if (cityName) {
                    // Utiliser Nominatim pour la recherche simple de la ville
                    fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(cityName)}&format=json&limit=1`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                // Afficher un cercle approximatif de la ville
                                const cityCenter = [parseFloat(data[0].lat), parseFloat(data[0].lon)];
                                L.circle(cityCenter, {
                                    radius: 2000, // 2 km de rayon
                                    color: '#3388ff',
                                    weight: 1,
                                    opacity: 0.8,
                                    fillColor: '#3388ff',
                                    fillOpacity: 0.1
                                }).addTo(map);

                                // Ajuster la vue pour montrer la zone
                                map.fitBounds([
                                    [parseFloat(data[0].boundingbox[0]), parseFloat(data[0].boundingbox[2])],
                                    [parseFloat(data[0].boundingbox[1]), parseFloat(data[0].boundingbox[3])]
                                ]);
                            }
                        })
                        .catch(error => {
                            console.error('Erreur lors de la recherche de la ville:', error);
                        });
                }
            } catch (error) {
                console.error('Erreur d\'initialisation de la carte:', error);
                document.getElementById('mission-map').innerHTML = '<p class="text-gray-500 p-4">Impossible de charger la carte.</p>';
            }

            // Gestion du bouton de candidature
            const applyButton = document.getElementById('apply-button');
            const applyModal = document.getElementById('apply-modal');
            const closeModal = document.getElementById('close-modal');
            const cancelApply = document.getElementById('cancel-apply');
            const applyForm = document.getElementById('apply-form');

            // Remplacez votre gestionnaire d'événements actuel par celui-ci
            if (applyButton) {
                applyButton.addEventListener('click', function() {
                    // Désactiver les interactions avec la carte
                    if (map) {
                        map.dragging.disable();
                        map.touchZoom.disable();
                        map.doubleClickZoom.disable();
                        map.scrollWheelZoom.disable();
                        map.boxZoom.disable();
                        map.keyboard.disable();
                        if (map.tap) map.tap.disable();
                    }

                    // Appliquer une classe pour griser la carte
                    document.getElementById('mission-map').classList.add('opacity-50');

                    // Afficher la modal
                    applyModal.classList.remove('hidden');
                });
            }

            // Modifier également les gestionnaires de fermeture
            if (closeModal) {
                closeModal.addEventListener('click', function() {
                    // Réactiver la carte
                    if (map && !<?php echo $has_applied ? 'true' : 'false'; ?>) {
                        map.dragging.enable();
                        map.touchZoom.enable();
                        map.doubleClickZoom.enable();
                        map.scrollWheelZoom.enable();
                        map.boxZoom.enable();
                        map.keyboard.enable();
                        if (map.tap) map.tap.enable();

                        // Restaurer l'opacité
                        document.getElementById('mission-map').classList.remove('opacity-50');
                    }

                    applyModal.classList.add('hidden');
                });
            }

            // Faire de même pour le bouton d'annulation
            if (cancelApply) {
                cancelApply.addEventListener('click', function() {
                    // Réactiver la carte
                    if (map && !<?php echo $has_applied ? 'true' : 'false'; ?>) {
                        map.dragging.enable();
                        map.touchZoom.enable();
                        map.doubleClickZoom.enable();
                        map.scrollWheelZoom.enable();
                        map.boxZoom.enable();
                        map.keyboard.enable();
                        if (map.tap) map.tap.enable();

                        // Restaurer l'opacité
                        document.getElementById('mission-map').classList.remove('opacity-50');
                    }

                    applyModal.classList.add('hidden');
                });
            }

            // Soumission du formulaire de candidature
            if (applyForm) {
                applyForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(applyForm);

                    fetch('../backend/apply_mission.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Votre candidature a bien été envoyée !');
                                // Redirection ou rechargement de la page
                                window.location.reload();
                            } else {
                                alert('Erreur : ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Une erreur est survenue lors de l\'envoi de votre candidature.');
                        });
                });
            }
        });
    </script>
</body>

</html>