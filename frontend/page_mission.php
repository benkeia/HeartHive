<?php
session_start();
require_once '../backend/db.php';

// Vérification que l'utilisateur est connecté et est une association
if (!isset($_SESSION['user_id']) || !isset($_SESSION['type']) || $_SESSION['type'] != 1) {
    echo '<script>window.location.href = "login.php";</script>';
    exit;
}

// Inclure le header
include './include/header.php';

// Récupération de l'ID de la mission depuis l'URL
$mission_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Vérification de l'existence de la mission et qu'elle appartient bien à cette association
$stmt = $conn->prepare("SELECT * FROM missions WHERE mission_id = ? AND association_id = ?");
$stmt->bind_param("ii", $mission_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<script>window.location.href = "index_asso.php";</script>';
    exit;
}

$mission = $result->fetch_assoc();

// Vérifier si la table applications existe dans la base de données
$applications = [];
$accepted_count = 0;

$check_table_query = "SHOW TABLES LIKE 'applications'";
$tables_result = $conn->query($check_table_query);
$applications_table_exists = ($tables_result->num_rows > 0);

if ($applications_table_exists) {
    // Si la table existe, récupérer les candidatures
    try {
        $stmt = $conn->prepare("
            SELECT a.*, u.first_name, u.last_name, u.email, u.profile_picture, u.bio
            FROM applications a
            JOIN users u ON a.user_id = u.id
            WHERE a.mission_id = ?
            ORDER BY a.created_at DESC
        ");
        $stmt->bind_param("i", $mission_id);
        $stmt->execute();
        $applications_result = $stmt->get_result();

        while ($row = $applications_result->fetch_assoc()) {
            $applications[] = $row;
            if ($row['status'] == 'accepted') {
                $accepted_count++;
            }
        }
    } catch (Exception $e) {
        // En cas d'erreur, on continue avec un tableau vide
    }
}

// Utiliser volunteers_registered si disponible, sinon calculer à partir des candidatures
$places_remaining = $mission['volunteers_needed'] - ($mission['volunteers_registered'] ?? $accepted_count);

// Préparation des données pour l'affichage
$location = json_decode($mission['location'], true);
$location_name = isset($location['name']) ? $location['name'] : 'Non précisé';
$coordinates = isset($location['coordinates']) ? $location['coordinates'] : [2.333333, 48.866667]; // Paris par défaut

$skills = json_decode($mission['skills_required'], true) ?: [];
$tags = json_decode($mission['tags'], true) ?: [];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de la mission - <?php echo htmlspecialchars($mission['title']); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.js"></script>
    <style>
        :root {
            --primary: #CF3275;
            --primary-light: #FFE9EF;
            --primary-hover: #ffd6e2;
            --text-primary: #374151;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #F9FAFB;
        }

        .page-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background-color: #b92864;
            transform: translateY(-1px);
        }

        .btn-outline {
            background-color: white;
            color: var(--primary);
            border: 1px solid var(--primary);
            padding: 0.5rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-outline:hover {
            background-color: var(--primary-light);
            transform: translateY(-1px);
        }

        .tag {
            display: inline-flex;
            align-items: center;
            background-color: var(--primary-light);
            color: var(--primary);
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }

        #map {
            height: 300px;
            border-radius: 0.5rem;
            z-index: 1;
            border: 1px solid #E5E7EB;
        }

        .leaflet-container {
            border-radius: 0.5rem;
        }

        .status-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .status-accepted {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .status-rejected {
            background-color: #FEE2E2;
            color: #B91C1C;
        }

        .profile-picture {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8 mt-20">
        <!-- En-tête de la mission -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($mission['title']); ?></h1>
                <div class="flex flex-wrap items-center mt-2">
                    <span class="text-gray-600 mr-4">
                        <i class="fas fa-map-marker-alt mr-2"></i><?php echo htmlspecialchars($location_name); ?>
                    </span>
                    <span class="text-gray-600">
                        <i class="fas fa-users mr-2"></i><?php echo $places_remaining; ?> place(s) restante(s)
                    </span>
                </div>
            </div>
            <div class="flex space-x-3">
                <button onclick="window.location.href='edit_mission.php?id=<?php echo $mission_id; ?>'" class="btn-outline">
                    <i class="fas fa-edit mr-2"></i>Modifier
                </button>
                <button onclick="confirmDelete(<?php echo $mission_id; ?>)" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    <i class="fas fa-trash-alt mr-2"></i>Supprimer
                </button>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informations de la mission -->
            <div class="lg:col-span-2">
                <div class="card p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Détails de la mission</h2>

                    <?php if (!empty($mission['image_url'])): ?>
                        <div class="mb-4">
                            <img src="<?php echo htmlspecialchars($mission['image_url']); ?>" alt="<?php echo htmlspecialchars($mission['title']); ?>" class="rounded-lg w-full h-64 object-cover">
                        </div>
                    <?php endif; ?>

                    <div class="prose max-w-none text-gray-700">
                        <?php echo nl2br(htmlspecialchars($mission['description'])); ?>
                    </div>

                    <?php if (!empty($skills)): ?>
                        <div class="mt-6">
                            <h3 class="text-md font-medium text-gray-700 mb-2">Compétences recherchées</h3>
                            <div class="flex flex-wrap">
                                <?php foreach ($skills as $skill): ?>
                                    <span class="tag">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        <?php echo htmlspecialchars($skill); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($tags)): ?>
                        <div class="mt-4">
                            <h3 class="text-md font-medium text-gray-700 mb-2">Tags</h3>
                            <div class="flex flex-wrap">
                                <?php foreach ($tags as $tag): ?>
                                    <span class="tag bg-blue-100 text-blue-800">
                                        <i class="fas fa-tag mr-1"></i>
                                        <?php echo htmlspecialchars($tag); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Carte de localisation -->
                <div class="card p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Localisation</h2>
                    <div id="map" class="w-full"></div>
                </div>
            </div>

            <!-- Sidebar avec candidatures -->
            <div class="lg:col-span-1">
                <div class="card p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Statistiques de la mission</h2>
                    <div class="flex justify-between mb-4 items-center">
                        <div>
                            <span class="text-gray-600">Places totales</span>
                            <p class="text-2xl font-bold"><?php echo $mission['volunteers_needed']; ?></p>
                        </div>
                        <div>
                            <span class="text-gray-600">Places restantes</span>
                            <p class="text-2xl font-bold text-<?php echo $places_remaining > 0 ? 'green' : 'red'; ?>-600"><?php echo $places_remaining; ?></p>
                        </div>
                    </div>
                    <div class="flex justify-between mb-4 items-center">
                        <div>
                            <span class="text-gray-600">Créé le</span>
                            <p class="text-lg font-medium"><?php echo date('d/m/Y', strtotime($mission['created_at'])); ?></p>
                        </div>
                        <div>
                            <span class="text-gray-600">Dernière modification</span>
                            <p class="text-lg font-medium"><?php echo date('d/m/Y', strtotime($mission['updated_at'])); ?></p>
                        </div>
                    </div>
                </div>

                <?php if ($applications_table_exists): ?>
                    <div class="card p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold text-gray-800">Candidatures</h2>
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                <?php echo count($applications); ?>
                            </span>
                        </div>

                        <?php if (empty($applications)): ?>
                            <div class="text-center py-6 text-gray-600">
                                <i class="fas fa-inbox text-4xl mb-3 text-gray-400"></i>
                                <p>Aucune candidature pour le moment</p>
                            </div>
                        <?php else: ?>
                            <div class="divide-y divide-gray-200">
                                <?php foreach ($applications as $app): ?>
                                    <div class="py-4 animate-fade-in">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <img src="<?php echo !empty($app['profile_picture']) ? htmlspecialchars($app['profile_picture']) : 'assets/default_avatar.png'; ?>"
                                                    alt="<?php echo htmlspecialchars($app['first_name']); ?>"
                                                    class="profile-picture mr-3">
                                                <div>
                                                    <h3 class="text-sm font-medium text-gray-900">
                                                        <?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?>
                                                    </h3>
                                                    <p class="text-xs text-gray-500"><?php echo date('d/m/Y à H:i', strtotime($app['created_at'])); ?></p>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="px-2 py-1 text-xs rounded-full status-<?php echo $app['status']; ?>">
                                                    <?php
                                                    $status_text = '';
                                                    switch ($app['status']) {
                                                        case 'pending':
                                                            $status_text = 'En attente';
                                                            break;
                                                        case 'accepted':
                                                            $status_text = 'Accepté';
                                                            break;
                                                        case 'rejected':
                                                            $status_text = 'Refusé';
                                                            break;
                                                        default:
                                                            $status_text = $app['status'];
                                                    }
                                                    echo $status_text;
                                                    ?>
                                                </span>
                                            </div>
                                        </div>

                                        <?php if ($app['status'] == 'pending'): ?>
                                            <div class="mt-3 flex justify-end space-x-2">
                                                <button onclick="updateApplication(<?php echo $app['id']; ?>, 'accepted', <?php echo $places_remaining; ?>)"
                                                    class="text-sm px-3 py-1 bg-green-100 text-green-800 rounded hover:bg-green-200"
                                                    <?php echo $places_remaining <= 0 ? 'disabled' : ''; ?>>
                                                    <i class="fas fa-check mr-1"></i>Accepter
                                                </button>
                                                <button onclick="updateApplication(<?php echo $app['id']; ?>, 'rejected')"
                                                    class="text-sm px-3 py-1 bg-red-100 text-red-800 rounded hover:bg-red-200">
                                                    <i class="fas fa-times mr-1"></i>Refuser
                                                </button>
                                            </div>
                                        <?php endif; ?>

                                        <div class="mt-2">
                                            <button onclick="viewUserProfile(<?php echo $app['user_id']; ?>)"
                                                class="text-xs text-pink-600 hover:text-pink-800 flex items-center">
                                                <i class="fas fa-user mr-1"></i>Voir le profil
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="card p-6">
                        <div class="text-center py-6">
                            <i class="fas fa-code text-4xl mb-3 text-pink-400"></i>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">Module de candidatures</h3>
                            <p class="text-gray-600 mb-4">Le système de candidatures n'est pas encore disponible.</p>
                            <p class="text-sm text-gray-500">Cette fonctionnalité sera bientôt disponible dans une prochaine mise à jour.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($applications_table_exists): ?>
        <!-- Modal profil bénévole -->
        <div id="userProfileModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Profil du bénévole</h3>
                    <button onclick="closeModal('userProfileModal')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="userProfileContent" class="space-y-4">
                    <!-- Le contenu sera chargé dynamiquement -->
                    <div class="text-center py-10">
                        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-pink-500 mx-auto"></div>
                        <p class="mt-3 text-gray-600">Chargement du profil...</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script>
        // Variables globales
        let missionMap = null;
        const missionData = <?php echo json_encode([
                                'lat' => $coordinates[1] ?? 48.866667,
                                'lon' => $coordinates[0] ?? 2.333333,
                                'title' => $mission['title'],
                                'address' => $location_name
                            ]); ?>;

        // Initialisation au chargement du document
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser la carte
            initializeMap();

            // Animer les entrées
            animateEntries();
        });

        // Fonction pour initialiser la carte
        function initializeMap() {
            // Initialisation de la carte
            missionMap = L.map('map').setView([missionData.lat, missionData.lon], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(missionMap);

            // Ajouter un marqueur à la position de la mission
            L.marker([missionData.lat, missionData.lon])
                .addTo(missionMap)
                .bindPopup(missionData.title + '<br>' + missionData.address)
                .openPopup();

            // S'assurer que la carte est bien rendue
            setTimeout(() => {
                missionMap.invalidateSize();
            }, 100);
        }

        // Fonction pour animer les entrées
        function animateEntries() {
            const entries = document.querySelectorAll('.animate-fade-in');
            entries.forEach((entry, index) => {
                entry.style.animationDelay = `${index * 0.1}s`;
            });
        }

        // Fonction pour ouvrir un modal
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        // Fonction pour fermer un modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Fonction pour confirmer la suppression
        function confirmDelete(missionId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette mission ?')) {
                window.location.href = `../backend/delete_mission.php?id=${missionId}`;
            }
        }

        <?php if ($applications_table_exists): ?>
            // Fonction pour mettre à jour le statut d'une candidature
            function updateApplication(applicationId, status, placesRemaining = null) {
                if (status === 'accepted' && placesRemaining <= 0) {
                    alert('Il n\'y a plus de places disponibles pour cette mission.');
                    return;
                }

                // Confirmation avant action
                let confirmMessage = status === 'accepted' ?
                    'Êtes-vous sûr de vouloir accepter cette candidature ?' :
                    'Êtes-vous sûr de vouloir refuser cette candidature ?';

                if (confirm(confirmMessage)) {
                    fetch(`../backend/update_application.php`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `application_id=${applicationId}&status=${status}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Recharger la page pour afficher les changements
                                location.reload();
                            } else {
                                alert('Une erreur est survenue : ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Une erreur est survenue lors de la mise à jour de la candidature.');
                        });
                }
            }

            // Fonction pour voir le profil d'un bénévole
            function viewUserProfile(userId) {
                openModal('userProfileModal');
                const contentDiv = document.getElementById('userProfileContent');

                // Afficher un loader pendant le chargement
                contentDiv.innerHTML = `
                <div class="text-center py-10">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-pink-500 mx-auto"></div>
                    <p class="mt-3 text-gray-600">Chargement du profil...</p>
                </div>
            `;

                // Charger les données du profil
                fetch(`../backend/get_user_profile.php?id=${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const user = data.user;
                            // Formater le contenu du profil
                            contentDiv.innerHTML = `
                            <div class="flex flex-col items-center">
                                <img src="${user.profile_picture || 'assets/default_avatar.png'}" 
                                     alt="${user.first_name}" 
                                     class="w-24 h-24 rounded-full object-cover">
                                <h4 class="mt-4 text-xl font-semibold">${user.first_name} ${user.last_name}</h4>
                                <p class="text-gray-500">${user.email}</p>
                            </div>
                            ${user.bio ? `<div class="mt-4">
                                <h5 class="font-medium text-gray-700">À propos</h5>
                                <p class="mt-1 text-gray-600">${user.bio}</p>
                            </div>` : ''}
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <button onclick="contactUser(${user.id})" 
                                        class="w-full py-2 bg-pink-600 text-white rounded-md hover:bg-pink-700 transition-colors">
                                    <i class="fas fa-envelope mr-2"></i>Contacter
                                </button>
                            </div>
                        `;
                        } else {
                            contentDiv.innerHTML = `
                            <div class="text-center py-8">
                                <i class="fas fa-exclamation-circle text-3xl text-red-500"></i>
                                <p class="mt-2 text-gray-600">${data.message || "Impossible de charger le profil."}</p>
                            </div>
                        `;
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        contentDiv.innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-exclamation-circle text-3xl text-red-500"></i>
                            <p class="mt-2 text-gray-600">Une erreur est survenue lors du chargement du profil.</p>
                        </div>
                    `;
                    });
            }

            // Fonction pour contacter un bénévole
            function contactUser(userId) {
                // Rediriger vers la messagerie
                window.location.href = `messaging.php?to=${userId}`;
            }
        <?php endif; ?>
    </script>
</body>

</html>