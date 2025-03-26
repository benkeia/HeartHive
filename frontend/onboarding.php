<?php
session_start();
require_once '../backend/db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Vérifier si l'email est dans la session (au cas où l'utilisateur vient de s'inscrire)
    if (isset($_SESSION['user_mail'])) {
        // Récupérer l'ID utilisateur depuis l'email
        $stmt = $conn->prepare("SELECT user_id FROM user WHERE user_mail = ?");
        $stmt->bind_param("s", $_SESSION['user_mail']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $_SESSION['user_id'] = $row['user_id'];
        } else {
            header('Location: loginPage.php');
            exit;
        }
    } else {
        header('Location: loginPage.php');
        exit;
    }
}

// Vérifier si l'onboarding a déjà été effectué
/* if (isset($_SESSION['onboarding_completed']) && $_SESSION['onboarding_completed'] === true) {
    // Rediriger selon le type d'utilisateur
    header('Location: ' . ($_SESSION['type'] == 1 ? 'index_asso.php' : 'index.php'));
    exit;
} */

// Utiliser des valeurs par défaut pour les intérêts et compétences
$interests = [
    "Éducation",
    "Environnement",
    "Santé",
    "Social",
    "Culture",
    "Sport",
    "Humanitaire",
    "Animaux",
    "Développement durable",
    "Droits humains",
    "Insertion",
    "Patrimoine",
    "Handicap",
    "Jeunesse",
    "Seniors"
];

$skills = [
    "Animation",
    "Communication",
    "Informatique",
    "Cuisine",
    "Bricolage",
    "Jardinage",
    "Médical",
    "Linguistique",
    "Artistique",
    "Photographique",
    "Administratif",
    "Enseignement",
    "Comptabilité",
    "Juridique",
    "Logistique",
    "Gestion de projet",
    "Conduite",
    "Manutention",
    "Accueil",
    "Accompagnement"
];

// Essayer de récupérer des valeurs depuis la base de données si les tables existent
try {
    // Vérifier si la table interests existe
    $interests_check = $conn->query("SHOW TABLES LIKE 'interests'");
    if ($interests_check->num_rows > 0) {
        $interests_query = "SELECT DISTINCT name FROM interests ORDER BY name";
        $interests_result = $conn->query($interests_query);

        if ($interests_result && $interests_result->num_rows > 0) {
            $interests = [];
            while ($row = $interests_result->fetch_assoc()) {
                $interests[] = $row['name'];
            }
        }
    }

    // Vérifier si la table skills existe
    $skills_check = $conn->query("SHOW TABLES LIKE 'skills'");
    if ($skills_check->num_rows > 0) {
        $skills_query = "SELECT DISTINCT name FROM skills ORDER BY name";
        $skills_result = $conn->query($skills_query);

        if ($skills_result && $skills_result->num_rows > 0) {
            $skills = [];
            while ($row = $skills_result->fetch_assoc()) {
                $skills[] = $row['name'];
            }
        }
    }
} catch (Exception $e) {
    // En cas d'erreur, on continue avec les valeurs par défaut
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur HeartHive - Compléter votre profil</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/dispo.js"></script>
    <style>
        :root {
            --primary: #CF3275;
            --primary-light: #FFE9EF;
            --secondary: #FB8E00;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #F9FAFB;
        }

        .progress-container {
            width: 100%;
            height: 8px;
            background-color: #e0e0e0;
            border-radius: 4px;
            margin-bottom: 40px;
        }

        .progress-bar {
            height: 8px;
            border-radius: 4px;
            background-color: var(--primary);
            transition: width 0.5s ease;
        }

        .step {
            display: none;
            animation: fadeIn 0.5s ease-in-out;
        }

        .step.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tag-list {
            max-height: 250px;
            overflow-y: auto;
        }

        .tag-item {
            transition: all 0.2s;
            cursor: pointer;
        }

        .tag-item.selected {
            background-color: var(--primary-light);
            color: var(--primary);
            border-color: var(--primary);
        }

        .tab {
            transition: all 0.3s;
        }

        .tab.active {
            background-color: white;
            color: var(--primary);
            border-bottom: 3px solid var(--primary);
        }

        .tab:hover {
            background-color: #f3f4f6;
        }

        /* Ajouter ces styles dans la section <style> de votre fichier */
        .schedule-container {
            max-height: 320px;
            overflow-y: auto;
        }

        .schedule-grid {
            min-width: 100%;
        }

        .time-slot {
            min-height: 24px;
        }

        .time-slot.selected {
            background-color: var(--primary-light);
            border-color: var(--primary);
        }

        /* Supprimez ces styles */
        /* Styles pour le modal de cropping */
        #cropper-modal {
            transition: opacity 0.3s ease;
        }

        #cropper-modal.hidden {
            opacity: 0;
            pointer-events: none;
        }

        #cropper-image {
            display: block;
            max-width: 100%;
        }

        .cropper-container {
            max-width: 100%;
            max-height: 50vh;
        }

        /* Améliorer les boutons du cropper */
        .cropper-point,
        .cropper-line {
            background-color: var(--secondary);
        }

        /* Réduire la taille du conteneur si nécessaire sur petit écran */
        @media (max-width: 640px) {
            #cropper-modal .max-w-2xl {
                max-width: 95%;
            }

            #cropper-image {
                max-height: 40vh;
            }
        }
    </style>

</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- Logo et entête -->
        <div class="bg-white shadow">
            <div class="container mx-auto px-4 py-6">
                <div class="flex justify-center items-center">
                    <img src="assets/icons/logo.svg" alt="HeartHive Logo" class="h-12">
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="flex-grow">
            <div class="container mx-auto px-4 py-10 max-w-4xl">
                <h1 class="text-3xl font-bold text-center text-gray-800 mb-2">Bienvenue sur HeartHive</h1>
                <p class="text-center text-gray-600 mb-8">Quelques étapes rapides pour personnaliser votre expérience</p>

                <!-- Barre de progression -->
                <div class="progress-container">
                    <div id="progress-bar" class="progress-bar" style="width: 25%"></div>
                </div>

                <!-- Étapes -->
                <div id="onboarding-steps">
                    <!-- Étape 1: Choix du type d'utilisateur -->
                    <div class="step active" id="step-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Vous êtes...</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Option Bénévole -->
                            <div class="user-type-option bg-white rounded-lg shadow-md p-6 border-2 border-transparent hover:border-pink-300 transition-all cursor-pointer" data-type="0">
                                <div class="mb-4 text-center">
                                    <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-pink-100 text-pink-600 mb-4">
                                        <i class="fas fa-user-alt text-2xl"></i>
                                    </div>
                                </div>
                                <h3 class="text-xl font-semibold text-center">Bénévole</h3>
                                <p class="text-gray-600 text-center mt-2">
                                    Je souhaite donner de mon temps pour aider des associations
                                </p>
                                <div class="mt-4 text-center">
                                    <button class="bg-pink-50 hover:bg-pink-100 text-pink-600 font-medium py-2 px-4 rounded-md transition">
                                        Choisir
                                    </button>
                                </div>
                            </div>

                            <!-- Option Association -->
                            <div class="user-type-option bg-white rounded-lg shadow-md p-6 border-2 border-transparent hover:border-orange-300 transition-all cursor-pointer" data-type="1">
                                <div class="mb-4 text-center">
                                    <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-orange-100 text-orange-500 mb-4">
                                        <i class="fas fa-users text-2xl"></i>
                                    </div>
                                </div>
                                <h3 class="text-xl font-semibold text-center">Association</h3>
                                <p class="text-gray-600 text-center mt-2">
                                    Je représente une association et recherche des bénévoles
                                </p>
                                <div class="mt-4 text-center">
                                    <button class="bg-orange-50 hover:bg-orange-100 text-orange-500 font-medium py-2 px-4 rounded-md transition">
                                        Choisir
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Étape 2: Formulaire pour bénévole -->
                    <div class="step" id="step-2-volunteer">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Complétez votre profil de bénévole</h2>

                        <!-- Onglets -->
                        <div class="flex border-b border-gray-200 mb-6">
                            <button class="tab active flex-1 py-3 px-4 text-center font-medium" data-tab="interests">
                                Centres d'intérêts
                            </button>
                            <button class="tab flex-1 py-3 px-4 text-center font-medium" data-tab="skills">
                                Compétences
                            </button>
                            <button class="tab flex-1 py-3 px-4 text-center font-medium" data-tab="availability">
                                Disponibilités
                            </button>
                        </div>

                        <!-- Contenu des onglets -->
                        <div class="tab-content">
                            <!-- Centres d'intérêts -->
                            <div class="tab-pane active" id="interests-pane">
                                <p class="mb-4 text-gray-600">Sélectionnez les causes qui vous tiennent à cœur :</p>

                                <div class="tag-list flex flex-wrap gap-2 mb-6">
                                    <?php foreach ($interests as $interest): ?>
                                        <div class="tag-item px-3 py-2 bg-gray-100 rounded-lg text-sm hover:bg-gray-200" data-value="<?php echo htmlspecialchars($interest); ?>">
                                            <?php echo htmlspecialchars($interest); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Ajouter un centre d'intérêt personnalisé
                                    </label>
                                    <div class="flex">
                                        <input type="text" id="custom-interest" class="flex-grow border-gray-300 rounded-l-md shadow-sm focus:ring-pink-500 focus:border-pink-500">
                                        <button id="add-custom-interest" class="bg-pink-600 text-white px-4 py-2 rounded-r-md hover:bg-pink-700">
                                            Ajouter
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Compétences -->
                            <div class="tab-pane hidden" id="skills-pane">
                                <p class="mb-4 text-gray-600">Sélectionnez vos compétences et savoir-faire :</p>

                                <div class="tag-list flex flex-wrap gap-2 mb-6">
                                    <?php foreach ($skills as $skill): ?>
                                        <div class="tag-item px-3 py-2 bg-gray-100 rounded-lg text-sm hover:bg-gray-200" data-value="<?php echo htmlspecialchars($skill); ?>">
                                            <?php echo htmlspecialchars($skill); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Ajouter une compétence personnalisée
                                    </label>
                                    <div class="flex">
                                        <input type="text" id="custom-skill" class="flex-grow border-gray-300 rounded-l-md shadow-sm focus:ring-pink-500 focus:border-pink-500">
                                        <button id="add-custom-skill" class="bg-pink-600 text-white px-4 py-2 rounded-r-md hover:bg-pink-700">
                                            Ajouter
                                        </button>
                                    </div>
                                </div>
                            </div>


                            <!-- Disponibilités -->
                            <div class="tab-pane hidden" id="availability-pane">
                                <p class="mb-4 text-gray-600">Indiquez vos disponibilités habituelles :</p>

                                <div class="bg-white rounded-lg shadow-md p-4 mb-4">
                                    <div class="legend flex gap-4 text-xs text-gray-700 mb-3">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-pink-100 border border-pink-300 rounded mr-1"></div>
                                            <span>Disponible</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-white border border-gray-300 rounded mr-1"></div>
                                            <span>Indisponible</span>
                                        </div>
                                    </div>

                                    <div class="schedule-container border border-gray-200 rounded">
                                        <!-- En-têtes des jours -->
                                        <div class="grid grid-cols-8 border-b border-gray-200">
                                            <div class="py-2 px-1 text-center text-xs font-semibold text-gray-600"></div>
                                            <?php
                                            $days = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
                                            foreach ($days as $day): ?>
                                                <div class="py-2 px-1 text-center text-xs font-semibold text-gray-600 border-l border-gray-200">
                                                    <?php echo $day; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <!-- Grille des heures et créneaux -->
                                        <div class="schedule-grid">
                                            <?php
                                            $hours = [];
                                            for ($i = 8; $i <= 21; $i++) {
                                                $hours[] = $i . ':00';
                                            }

                                            foreach ($hours as $hourIndex => $hour): ?>
                                                <div class="grid grid-cols-8 border-b border-gray-100 last:border-b-0">
                                                    <!-- Étiquette de l'heure -->
                                                    <div class="py-1 px-1 text-[10px] text-gray-500 flex items-center justify-center bg-gray-50 border-r border-gray-200">
                                                        <?php echo $hour; ?>
                                                    </div>

                                                    <!-- Créneaux pour chaque jour -->
                                                    <?php foreach ($days as $dayIndex => $day): ?>
                                                        <div class="time-slot py-1 border-l border-gray-200 cursor-pointer transition-colors hover:bg-pink-50"
                                                            data-day="<?php echo $day; ?>"
                                                            data-day-index="<?php echo $dayIndex; ?>"
                                                            data-hour="<?php echo $hour; ?>"
                                                            data-hour-index="<?php echo $hourIndex; ?>">
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <div class="mt-4 border-t border-gray-200 pt-3">
                                        <div class="text-sm font-medium text-gray-700 mb-2">Récapitulatif de vos disponibilités :</div>
                                        <div id="availability-summary" class="flex flex-wrap gap-2">
                                            <span class="text-sm text-gray-500">Aucune disponibilité sélectionnée</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-between">
                            <button id="back-step-2" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-gray-700 hover:bg-gray-50">Précédent</button>
                            <button id="next-step-2" class="px-4 py-2 bg-pink-600 text-white rounded-md shadow-sm hover:bg-pink-700">Continuer</button>
                        </div>
                    </div>

                    <!-- Étape 2: Formulaire pour association -->
                    <div class="step" id="step-2-association">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Création du profil d'association</h2>

                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <div class="text-center py-8">
                                <img src="assets/icons/maintenance.svg" alt="En construction" class="w-32 h-32 mx-auto mb-4"
                                    onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/6911/6911990.png';">

                                <h3 class="text-xl font-semibold text-gray-800 mb-3">Fonctionnalité bientôt disponible</h3>
                                <p class="text-gray-600 mb-6">
                                    La création de profil pour les associations sera disponible prochainement.
                                    Notre équipe travaille actuellement pour finaliser cette fonctionnalité.
                                </p>

                                <p class="text-sm text-gray-500 mb-6">
                                    Pour obtenir de l'aide ou des informations supplémentaires, veuillez nous contacter.
                                </p>

                                <div class="flex justify-center space-x-4">
                                    <a href="mailto:contact@hearthive.org" class="text-pink-600 hover:text-pink-800 font-medium">
                                        <i class="fas fa-envelope mr-1"></i> Nous contacter
                                    </a>
                                    <span class="text-gray-300">|</span>
                                    <a href="javascript:history.back()" class="text-pink-600 hover:text-pink-800 font-medium">
                                        <i class="fas fa-arrow-left mr-1"></i> Retour
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-between">
                            <button id="back-step-2-asso" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-gray-700 hover:bg-gray-50">Précédent</button>
                            <button id="next-step-2-asso" class="px-4 py-2 bg-pink-600 text-white rounded-md shadow-sm hover:bg-pink-700">Continuer</button>
                        </div>
                    </div>

                    <!-- Étape 3: Finalisation -->
                    <div class="step" id="step-3">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center h-24 w-24 rounded-full bg-green-100 text-green-500 mb-6">
                                <i class="fas fa-check text-4xl"></i>
                            </div>

                            <h2 class="text-2xl font-bold text-gray-800 mb-3">Profil complété avec succès !</h2>
                            <p class="text-gray-600 mb-8">Votre profil est maintenant configuré et vous êtes prêt à utiliser HeartHive</p>

                            <button id="finish-button" class="px-6 py-3 bg-pink-600 text-white rounded-md shadow-md hover:bg-pink-700 transition mx-auto block">
                                Commencer l'aventure
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let map = null;
        let marker = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Variables pour le processus d'onboarding
            let currentStep = 1;
            let userType = null;
            let selectedInterests = [];
            let selectedSkills = [];
            // Ne pas initialiser ici car window.availability est déjà défini dans dispo.js

            // Mise à jour de la barre de progression
            function updateProgressBar() {
                const totalSteps = 3;
                const progressBar = document.getElementById('progress-bar');
                const progress = (currentStep / totalSteps) * 100;
                progressBar.style.width = `${progress}%`;
            }

            // Navigation entre les étapes
            function showStep(stepNumber) {
                const steps = document.querySelectorAll('.step');
                steps.forEach(step => step.classList.remove('active'));

                let nextStep;
                if (stepNumber === 2) {
                    if (userType === "0") {
                        nextStep = document.getElementById('step-2-volunteer');
                    } else {
                        nextStep = document.getElementById('step-2-association');
                    }
                } else {
                    nextStep = document.getElementById(`step-${stepNumber}`);
                }

                nextStep.classList.add('active');
                currentStep = stepNumber;
                updateProgressBar();
            }

            // Choix du type d'utilisateur
            const userTypeOptions = document.querySelectorAll('.user-type-option');
            userTypeOptions.forEach(option => {
                option.addEventListener('click', function() {
                    userTypeOptions.forEach(opt => opt.classList.remove('border-pink-500', 'border-orange-500'));

                    userType = this.dataset.type;
                    if (userType === "0") {
                        this.classList.add('border-pink-500');
                    } else {
                        this.classList.add('border-orange-500');
                    }

                    setTimeout(() => {
                        showStep(2);
                    }, 300);
                });
            });

            // Navigation entre les onglets (bénévole)
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabId = this.dataset.tab;

                    // Mise à jour des onglets actifs
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    // Mise à jour du contenu affiché
                    document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.add('hidden'));
                    document.getElementById(`${tabId}-pane`).classList.remove('hidden');
                });
            });

            // Gestion des tags (intérêts et compétences)
            const tagItems = document.querySelectorAll('.tag-item');
            tagItems.forEach(item => {
                item.addEventListener('click', function() {
                    const value = this.dataset.value;
                    const list = this.closest('.tag-list');

                    if (list.id === 'interests-pane' || list.closest('#interests-pane')) {
                        // Gestion des intérêts
                        const index = selectedInterests.indexOf(value);
                        if (index === -1) {
                            selectedInterests.push(value);
                            this.classList.add('selected');
                        } else {
                            selectedInterests.splice(index, 1);
                            this.classList.remove('selected');
                        }
                    } else {
                        // Gestion des compétences
                        const index = selectedSkills.indexOf(value);
                        if (index === -1) {
                            selectedSkills.push(value);
                            this.classList.add('selected');
                        } else {
                            selectedSkills.splice(index, 1);
                            this.classList.remove('selected');
                        }
                    }
                });
            });

            // Ajout d'intérêt personnalisé
            document.getElementById('add-custom-interest').addEventListener('click', function() {
                const input = document.getElementById('custom-interest');
                const value = input.value.trim();

                if (value && !selectedInterests.includes(value)) {
                    selectedInterests.push(value);

                    // Créer un nouveau tag et l'ajouter à la liste
                    const tagList = document.querySelector('#interests-pane .tag-list');
                    const newTag = document.createElement('div');
                    newTag.className = 'tag-item px-3 py-2 bg-gray-100 rounded-lg text-sm hover:bg-gray-200 selected';
                    newTag.dataset.value = value;
                    newTag.textContent = value;
                    newTag.addEventListener('click', function() {
                        const index = selectedInterests.indexOf(value);
                        if (index !== -1) {
                            selectedInterests.splice(index, 1);
                            this.remove();
                        }
                    });

                    tagList.appendChild(newTag);
                    input.value = '';
                }
            });

            // Ajout de compétence personnalisée
            document.getElementById('add-custom-skill').addEventListener('click', function() {
                const input = document.getElementById('custom-skill');
                const value = input.value.trim();

                if (value && !selectedSkills.includes(value)) {
                    selectedSkills.push(value);

                    // Créer un nouveau tag et l'ajouter à la liste
                    const tagList = document.querySelector('#skills-pane .tag-list');
                    const newTag = document.createElement('div');
                    newTag.className = 'tag-item px-3 py-2 bg-gray-100 rounded-lg text-sm hover:bg-gray-200 selected';
                    newTag.dataset.value = value;
                    newTag.textContent = value;
                    newTag.addEventListener('click', function() {
                        const index = selectedSkills.indexOf(value);
                        if (index !== -1) {
                            selectedSkills.splice(index, 1);
                            this.remove();
                        }
                    });

                    tagList.appendChild(newTag);
                    input.value = '';
                }
            });

            // Gestion des disponibilités
            const availabilityCheckboxes = document.querySelectorAll('.availability-checkbox');
            availabilityCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const day = parseInt(this.dataset.day);
                    const slot = parseInt(this.dataset.slot);

                    if (this.checked) {
                        // Ajouter la disponibilité
                        availability.push({
                            day,
                            slot
                        });
                    } else {
                        // Retirer la disponibilité
                        const index = availability.findIndex(a => a.day === day && a.slot === slot);
                        if (index !== -1) {
                            availability.splice(index, 1);
                        }
                    }
                });
            });

            // Boutons de navigation
            document.getElementById('back-step-2').addEventListener('click', function() {
                showStep(1);
            });

            document.getElementById('next-step-2').addEventListener('click', function() {
                // Enregistrer les données du bénévole
                saveVolunteerData();
            });

            document.getElementById('back-step-2-asso').addEventListener('click', function() {
                showStep(1);
            });

            document.getElementById('next-step-2-asso').addEventListener('click', function() {
                // Remplacez la logique par un simple message
                alert("La création de profil pour les associations sera disponible prochainement.");
            });

            document.getElementById('finish-button').addEventListener('click', function() {
                // Redirection vers la page appropriée
                window.location.href = userType === "0" ? "index.php" : "index_asso.php";
            });

            // Enregistrer les données du bénévole
            function saveVolunteerData() {
                // Récupérer les disponibilités
                const availabilityData = window.availability || [];

                const data = {
                    user_type: 0,
                    interests: selectedInterests,
                    skills: selectedSkills,
                    availability: availabilityData // Utiliser la variable globale window.availability
                };

                console.log("Données à envoyer:", data);

                fetch('../backend/save_onboarding.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            showStep(3);
                        } else {
                            alert('Erreur: ' + result.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue lors de l\'enregistrement de vos données');
                    });
            }
        });

        // Dans votre script principal de onboarding.php

        // Gestion des onglets
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Masquer tous les contenus d'onglet
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.add('hidden');
                });

                // Désactiver tous les onglets
                document.querySelectorAll('.tab').forEach(t => {
                    t.classList.remove('active');
                });

                // Activer l'onglet courant
                this.classList.add('active');

                // Afficher le contenu correspondant
                const tabId = this.getAttribute('data-tab');
                document.getElementById(`${tabId}-pane`).classList.remove('hidden');
            });
        });

        // Ouvrir le modal de recadrage
        function openCropperModal(type, imageSrc) {
            currentImageType = type;
            const modal = document.getElementById('cropper-modal');
            const cropperImage = document.getElementById('cropper-image');

            // Définir le titre en fonction du type d'image
            document.getElementById('cropper-title').textContent = type === 'profile' ?
                'Recadrer la photo de profil (format carré)' :
                'Recadrer l\'image de couverture (format 16:9)';

            // Réinitialiser l'image
            cropperImage.src = ''; // Effacer l'image d'abord

            // Afficher le modal avec une légère animation
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.remove('hidden');

                // Charger l'image après que le modal soit visible
                cropperImage.src = imageSrc;

                // Détruire le cropper existant s'il y en a un
                if (cropper) {
                    cropper.destroy();
                }

                // Attendre que l'image soit chargée
                cropperImage.onload = function() {
                    // Initialiser le nouveau cropper
                    cropper = new Cropper(cropperImage, {
                        aspectRatio: cropperConfig[type].aspectRatio,
                        viewMode: 1,
                        autoCropArea: 0.8,
                        responsive: true,
                        zoomable: true,
                        guides: true,
                        background: true,
                        center: true,
                        minContainerWidth: 300,
                        minContainerHeight: 300,
                        minCropBoxWidth: 100,
                        minCropBoxHeight: 100
                    });
                };
            }, 50);
        }

        // Fermer le modal de recadrage
        function closeCropperModal() {
            const modal = document.getElementById('cropper-modal');

            // Ajouter une transition pour une fermeture en douceur
            modal.classList.add('hidden');

            // Détruire le cropper
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        }

        // Appliquer le recadrage
        function applyCrop() {
            if (!cropper) return;

            try {
                // Obtenir le canvas recadré avec la qualité et taille appropriées
                const canvas = cropper.getCroppedCanvas({
                    width: currentImageType === 'profile' ? 300 : 800,
                    height: currentImageType === 'profile' ? 300 : 450,
                    fillColor: '#fff',
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });

                if (!canvas) {
                    throw new Error("Impossible de créer le canvas");
                }

                // Convertir en base64 et mettre à jour l'aperçu
                const imageData = canvas.toDataURL('image/jpeg', 0.85);
                cropperConfig[currentImageType].previewElement.src = imageData;
                cropperConfig[currentImageType].hiddenInputElement.value = imageData;

                // Indiquer visuellement que l'image a été chargée avec succès
                const overlay = currentImageType === 'profile' ?
                    document.getElementById('profile-overlay') :
                    document.getElementById('background-overlay');

                overlay.innerHTML = '<span class="text-white text-sm bg-green-500 px-2 py-1 rounded">Image chargée</span>';

                // Fermer le modal
                closeCropperModal();
            } catch (error) {
                console.error("Erreur lors du recadrage:", error);
                alert("Une erreur s'est produite lors du recadrage de l'image. Veuillez réessayer.");
            }
        }
    </script>
</body>

</html>