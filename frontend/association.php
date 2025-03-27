<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($associationName) ? htmlspecialchars($associationName) : 'Association'; ?> - HeartHive</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- CSS Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>

<style>
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .mission-card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .mission-card>div:last-child {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .hero-section {
        position: relative;
        height: 400px;
        overflow: hidden;
        border-radius: 0 0 2rem 2rem;
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.7));
    }

    .hero-content {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 2rem;
        color: white;
    }

    .profile-picture {
        border: 4px solid white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .follow-btn {
        transition: all 0.3s ease;
    }

    .follow-btn:hover {
        transform: translateY(-2px);
    }

    .follow-btn:active {
        transform: translateY(0);
    }

    .stats-card {
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-5px);
    }

    .tab-active {
        color: #4a6cf7;
        border-bottom: 2px solid #4a6cf7;
    }

   /* Remplacez le style existant de la notification XP (vers la ligne 107) par ceci : */
.xp-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: linear-gradient(135deg, #4a6cf7, #6a88ff);
    color: white;
    padding: 12px 16px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    transform: translateX(120%);
    transition: transform 0.3s ease;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(74, 108, 247, 0.2);
    max-width: 300px; /* Ajout de cette ligne pour limiter la largeur */
    width: auto; /* Ajout de cette ligne pour s'assurer que la popup prend la largeur de son contenu */
}

.xp-notification.show {
    transform: translateX(0);
}

.xp-notification .icon {
    background: rgba(255, 255, 255, 0.2);
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-weight: bold;
    font-size: 18px;
    flex-shrink: 0; /* Ajout de cette ligne pour éviter que l'icône ne se rétrécisse */
}

.xp-notification .content {
    flex: 1; /* Ajout de cette ligne pour que le contenu prenne l'espace disponible */
    overflow: hidden; /* Empêcher le débordement du texte */
}

.xp-notification .points {
    font-weight: bold;
    font-size: 16px;
    white-space: nowrap; /* Éviter que le texte se brise sur plusieurs lignes */
}

.xp-notification .reason {
    font-size: 12px;
    opacity: 0.9;
    white-space: nowrap; /* Éviter que le texte se brise sur plusieurs lignes */
    text-overflow: ellipsis; /* Ajouter des points de suspension si le texte déborde */
    overflow: hidden; /* Cacher le texte qui déborde */
}
</style>

<body class="bg-gray-50">
    <?php
    include '../backend/db.php';
    include 'include/header.php';

    // Requête préparée
    $associationStatement = $conn->prepare("SELECT * FROM association WHERE association_id = ?");
    $associationStatement->bind_param("i", $associationId);

    // Remplacer cette partie
    if (!isset($_GET['id'])) {
        die("Aucune association sélectionnée. Veuillez préciser un ID d'association.");
    }
    $association_id = (int)$_GET['id'];
    $associationId = $association_id;

    $associationStatement->execute();

    // Récupération des résultats
    $result = $associationStatement->get_result();
    $association = null;

    if ($result->num_rows > 0) {
        $association = $result->fetch_assoc();
        $associationProfilePicture = htmlspecialchars($association['association_profile_picture']);
        $associationBackgroundImage = htmlspecialchars($association['association_background_image']);
        $associationName = htmlspecialchars($association['association_name']);
        $associationAdress = $association['association_adress'];
        $associationMail = htmlspecialchars($association['association_mail'] ?? '');
        $associationDesc = htmlspecialchars($association['association_desc']);
        $associationMission = htmlspecialchars($association['association_mission']);
    } else {
        die("Association non trouvée.");
    }

    // Fermer la requête
    $associationStatement->close();

    // Décodez la chaîne JSON d'adresse
    $decodedAddress = null;
    if (!empty($associationAdress)) {
        $associationAdress = trim($associationAdress);
        $decodedAddress = json_decode($associationAdress, true);
    }

    // Vérifier si l'utilisateur suit déjà cette association
    $isFollowing = false;
    $followButtonText = "Suivre l'association";
    $followButtonClass = "bg-blue-600 hover:bg-blue-700 text-white";

    if (isset($_SESSION['user_id'])) {
        $checkFollowStmt = $conn->prepare("SELECT * FROM postulation WHERE postulation_user_id_fk = ? AND postulation_association_id_fk = ?");
        $checkFollowStmt->bind_param("ii", $_SESSION['user_id'], $associationId);
        $checkFollowStmt->execute();
        $followResult = $checkFollowStmt->get_result();

        if ($followResult->num_rows > 0) {
            $isFollowing = true;
            $followButtonText = "Ne plus suivre";
            $followButtonClass = "bg-gray-200 hover:bg-gray-300 text-gray-800";
        }
        $checkFollowStmt->close();
    }

    // Compter le nombre de followers
    $followersStmt = $conn->prepare("SELECT COUNT(*) as count FROM postulation WHERE postulation_association_id_fk = ?");
    $followersStmt->bind_param("i", $associationId);
    $followersStmt->execute();
    $followersCount = $followersStmt->get_result()->fetch_assoc()['count'];
    $followersStmt->close();

    // Compter le nombre de missions
    $missionsCountStmt = $conn->prepare("SELECT COUNT(*) as count FROM missions WHERE association_id = ?");
    $missionsCountStmt->bind_param("i", $associationId);
    $missionsCountStmt->execute();
    $missionsCount = $missionsCountStmt->get_result()->fetch_assoc()['count'];
    $missionsCountStmt->close();
    ?>

    <main class="pb-12">
        <!-- Hero Section avec image de fond -->
        <section class="hero-section" style="background-image: url('<?php echo $associationBackgroundImage ?>'); background-size: cover; background-position: center;">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <div class="container mx-auto flex items-end">
                    <img class="w-24 h-24 md:w-32 md:h-32 rounded-full profile-picture object-cover" src="<?php echo $associationProfilePicture ?>" alt="<?php echo $associationName ?>">
                    <div class="ml-6">
                        <h1 class="text-3xl md:text-4xl font-bold"><?php echo $associationName ?></h1>
                        <p class="text-sm md:text-base text-gray-200 mt-2">
                            <?php echo isset($decodedAddress['name']) ? htmlspecialchars($decodedAddress['name']) : 'Adresse non spécifiée'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <div class="container mx-auto px-4 -mt-6 relative z-10">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="stats-card bg-white p-6 rounded-xl shadow-sm flex items-center">
                    <div class="p-3 rounded-full bg-blue-50 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800"><?php echo $followersCount; ?></h3>
                        <p class="text-sm text-gray-500">Abonnés</p>
                    </div>
                </div>

                <div class="stats-card bg-white p-6 rounded-xl shadow-sm flex items-center">
                    <div class="p-3 rounded-full bg-purple-50 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800"><?php echo $missionsCount; ?></h3>
                        <p class="text-sm text-gray-500">Missions proposées</p>
                    </div>
                </div>

                <div class="stats-card bg-white p-6 rounded-xl shadow-sm">
                    <form id="followForm" class="flex flex-col h-full justify-center items-center">
                        <input type="hidden" name="association_id" value="<?php echo $associationId; ?>">
                        <input type="hidden" name="action" value="<?php echo $isFollowing ? 'unfollow' : 'follow'; ?>">
                        <button type="submit" id="followBtn" class="follow-btn w-full py-3 px-4 rounded-lg font-medium transition-all <?php echo $followButtonClass; ?>">
                            <?php echo $followButtonText; ?>
                        </button>
                        <p class="text-xs text-gray-500 mt-2 text-center">
                            <?php echo $isFollowing ?
                                "Vous suivez cette association et recevrez ses actualités" :
                                "Suivez cette association pour être informé de ses nouvelles missions"; ?>
                        </p>
                    </form>
                </div>
            </div>

            <!-- Association Info with Tabs -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8">
                <div class="border-b border-gray-200">
                    <div class="flex">
                        <button class="tab-btn tab-active px-6 py-4 text-sm font-medium" data-tab="about">À propos</button>
                        <button class="tab-btn px-6 py-4 text-sm font-medium text-gray-500" data-tab="location">Localisation</button>
                        <button class="tab-btn px-6 py-4 text-sm font-medium text-gray-500" data-tab="contact">Contact</button>
                    </div>
                </div>

                <div class="p-6">
                    <div id="about-tab" class="tab-content">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Présentation</h2>
                        <p class="text-gray-600 mb-6"><?php echo $associationDesc ?></p>

                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Notre mission</h2>
                        <p class="text-gray-600"><?php echo $associationMission ?></p>
                    </div>

                    <div id="location-tab" class="tab-content hidden">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Où nous trouver</h2>
                        <div class="rounded-xl overflow-hidden shadow-md h-[400px]">
                            <div id="map" class="w-full h-full"></div>
                        </div>
                    </div>

                    <div id="contact-tab" class="tab-content hidden">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Nous contacter</h2>
                        <div class="bg-blue-50 p-4 rounded-lg mb-6">
                            <p class="text-blue-700">Vous pouvez entrer en contact avec l'association via les coordonnées ci-dessous.</p>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-6 w-6 text-blue-500 mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Email</p>
                                    <p class="text-sm text-gray-500"><?php echo !empty($associationMail) ? htmlspecialchars($associationMail) : 'Non renseigné'; ?></p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-6 w-6 text-blue-500 mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Adresse</p>
                                    <p class="text-sm text-gray-500">
                                        <?php echo isset($decodedAddress['name']) ? htmlspecialchars($decodedAddress['name']) : 'Adresse non spécifiée'; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Missions Section -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-800">Missions proposées</h2>
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">
                            <?php echo $missionsCount; ?> mission<?php echo $missionsCount !== 1 ? 's' : ''; ?>
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php
                        // Récupérer les missions de cette association
                        $missionsStmt = $conn->prepare("
                            SELECT mission_id, title, description, image_url, volunteers_needed, volunteers_registered, created_at, tags
                            FROM missions 
                            WHERE association_id = ?
                            ORDER BY created_at DESC
                        ");

                        $missionsStmt->bind_param("i", $associationId);
                        $missionsStmt->execute();
                        $missionsResult = $missionsStmt->get_result();

                        if ($missionsResult->num_rows > 0) {
                            while ($mission = $missionsResult->fetch_assoc()) {
                                // Traiter les tags s'ils existent
                                $tags = [];
                                if (!empty($mission['tags'])) {
                                    $tags = json_decode($mission['tags'], true) ?: [];
                                }

                                // Calculer les places restantes
                                $places_remaining = $mission['volunteers_needed'] - ($mission['volunteers_registered'] ?? 0);
                        ?>
                                <div class="mission-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border border-gray-100">
                                    <?php if (!empty($mission['image_url'])): ?>
                                        <div class="h-48 overflow-hidden">
                                            <img src="<?php echo htmlspecialchars($mission['image_url']); ?>" alt="<?php echo htmlspecialchars($mission['title']); ?>" class="w-full h-full object-cover">
                                        </div>
                                    <?php else: ?>
                                        <div class="h-48 bg-gradient-to-r from-blue-50 to-indigo-50 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                    <?php endif; ?>

                                    <div class="p-5">
                                        <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($mission['title']); ?></h3>

                                        <?php if (!empty($tags)): ?>
                                            <div class="flex flex-wrap gap-1 mb-3">
                                                <?php foreach ($tags as $tag): ?>
                                                    <span class="bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-full">
                                                        <?php echo htmlspecialchars($tag); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                            <?php echo htmlspecialchars(substr($mission['description'], 0, 150)) . (strlen($mission['description']) > 150 ? '...' : ''); ?>
                                        </p>

                                        <div class="flex items-center justify-between mt-auto pt-3 border-t border-gray-100">
                                            <div>
                                                <?php if ($places_remaining > 0): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3" />
                                                        </svg>
                                                        <?php echo $places_remaining; ?> place<?php echo $places_remaining > 1 ? 's' : ''; ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3" />
                                                        </svg>
                                                        Complet
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <a href="mission.php?id=<?php echo $mission['mission_id']; ?>" class="inline-flex items-center py-2 px-3 text-sm font-medium rounded-lg text-blue-700 hover:bg-blue-50 transition-colors">
                                                Détails
                                                <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            }
                            $missionsStmt->close();
                        } else {
                            ?>
                            <div class="col-span-full text-center py-10">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-600">Aucune mission disponible</h3>
                                <p class="text-gray-500 mt-2">Cette association n'a pas encore publié de missions.</p>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Pop-up -->
    <div id="popup" class="fixed inset-0 flex items-center justify-center bg-black/50 bg-opacity-50 hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-80 text-center">
            <div id="loading" class="flex flex-col items-center justify-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mb-4"></div>
                <span class="text-gray-700 font-medium">Traitement en cours...</span>
            </div>
            <div id="confirmation" class="hidden">
                <p id="confirmationMessage" class="font-bold"></p>
                <button id="closePop"
                    class="mt-4 py-2 px-4 bg-blue-600 hover:bg-blue-700 rounded-lg text-white font-medium transition-colors">Fermer</button>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Gestion des onglets
        document.addEventListener('DOMContentLoaded', function() {
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');

            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Retirer la classe active de tous les boutons
                    tabBtns.forEach(b => {
                        b.classList.remove('tab-active');
                        b.classList.add('text-gray-500');
                    });

                    // Ajouter la classe active au bouton cliqué
                    this.classList.add('tab-active');
                    this.classList.remove('text-gray-500');

                    // Cacher tous les contenus
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });

                    // Afficher le contenu correspondant
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId + '-tab').classList.remove('hidden');

                    // Initialiser la carte si on passe à l'onglet localisation
                    if (tabId === 'location') {
                        initMap();
                    }
                });
            });

            // Gestionnaire pour le suivi/désabonnement
            document.getElementById('followForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                // Mettre à jour l'URL pour refléter l'action de suivi plutôt que de postulation
                const url = formData.get('action') === 'follow' ? 'postulate.php' : 'unfollow_association.php';

                // Afficher la popup de chargement
                const popup = document.getElementById('popup');
                const loading = document.getElementById('loading');
                const confirmation = document.getElementById('confirmation');
                const confirmationMessage = document.getElementById('confirmationMessage');

                // Afficher la popup avec l'animation de chargement
                popup.classList.remove('hidden');
                loading.classList.remove('hidden');
                confirmation.classList.add('hidden');

                fetch(url, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Cacher le loading, afficher la confirmation
                        loading.classList.add('hidden');
                        confirmation.classList.remove('hidden');

                        if (data.status === 'success') {
                            // Définir le message de confirmation
                            confirmationMessage.textContent = data.message;
                            confirmationMessage.className = "font-bold text-green-600";

                            // Si XP ajouté, afficher la notification
                            if (data.xp_added && data.xp_points > 0) {
                                showXPNotification(data.xp_points, data.action === 'follow' ? "Association suivie!" : "Désabonnement effectué");
                            }
                        } else {
                            // Afficher l'erreur dans la popup
                            confirmationMessage.textContent = data.message;
                            confirmationMessage.className = "font-bold text-red-600";
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);

                        // Afficher l'erreur dans la popup
                        loading.classList.add('hidden');
                        confirmation.classList.remove('hidden');
                        confirmationMessage.textContent = "Une erreur est survenue lors de l'opération.";
                        confirmationMessage.className = "font-bold text-red-600";
                    });
            });

            // Ajouter le gestionnaire pour fermer la popup
            document.getElementById('closePop').addEventListener('click', function() {
                document.getElementById('popup').classList.add('hidden');
                window.location.reload(); // Recharger la page après fermeture
            });
        });

        // Fonction pour initialiser la carte
        function initMap() {
            // Récupérer les coordonnées dynamiquement passées par PHP
            let associationData = <?php echo json_encode($decodedAddress, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
            let associationName = "<?php echo $associationName; ?>";

            if (associationData && associationData.coordinates && associationData.coordinates.length === 2) {
                let lat = parseFloat(associationData.coordinates[1]); // Latitude
                let lon = parseFloat(associationData.coordinates[0]); // Longitude

                // Vérifier si la carte a déjà été initialisée
                if (window.myMap) {
                    window.myMap.remove();
                }

                // Initialisation de la carte Leaflet
                window.myMap = L.map('map').setView([lat, lon], 13); // Zoom par défaut

                // Ajouter la couche OpenStreetMap
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(window.myMap);

                // Ajouter un marqueur à la carte
                L.marker([lat, lon]).addTo(window.myMap)
                    .bindPopup('<b>' + associationName + '</b>') // Affiche le nom de l'association
                    .openPopup();
            } else {
                console.error("Erreur : Coordonnées invalides !");
                document.getElementById('map').innerHTML = '<div class="flex items-center justify-center h-full bg-gray-100 text-gray-500">Coordonnées non disponibles</div>';
            }
        }

        // Fonction pour afficher la notification XP
        function showXPNotification(points, message) {
            const notification = document.createElement('div');
            notification.className = 'xp-notification';
            notification.innerHTML = `
                <div class="icon">+</div>
                <div class="content">
                    <div class="points">+${points} XP</div>
                    <div class="reason">${message}</div>
                </div>
            `;

            document.body.appendChild(notification);

            // Animation d'affichage
            setTimeout(() => notification.classList.add('show'), 10);

            // Suppression après 3 secondes
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    </script>
</body>

</html>