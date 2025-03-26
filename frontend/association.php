<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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

<body>
    <?php
    include '../backend/db.php';
    include 'include/header.php';

    // Requête préparée
    $associationStatement = $conn->prepare("SELECT * FROM association WHERE association_id = ?");
    $associationStatement->bind_param("i", $associationId);

    if (!isset($_SESSION['association_id'])) {
        die("Aucune association sélectionnée.");
    }
    $association_id = $_SESSION['association_id'];

    // Remplacez 0 par la valeur souhaitée
    $associationId = $association_id;
    $associationStatement->execute();

    // Récupération des résultats
    $result = $associationStatement->get_result();
    while ($association = $result->fetch_assoc()) {
        $associationProfilePicture = htmlspecialchars($association['association_profile_picture']);
        $associationBackgroundImage = htmlspecialchars($association['association_background_image']);
        $associationName = htmlspecialchars($association['association_name']);
        $associationAdress = $association['association_adress'];
        $associationDesc = htmlspecialchars($association['association_desc']);
        $associationMission = htmlspecialchars($association['association_mission']);
    }
    // Fermer la requête
    $associationStatement->close();
    $associationAdress = trim($associationAdress);

    // Décodez la chaîne JSON
    $decodedAddress = json_decode($associationAdress, true);
    ?>

    <div class="mainAssociationContainer flex flex-col">
        <div class="topAssociationContainerBB flex">
            <div class="leftAssociationContainer p-10">
                <img class="rounded-3xl" src="<?php echo $associationBackgroundImage ?>" alt="">
                <div class="associationTitle flex gap-x-10 items-center py-10">
                    <img class="w-[75px] rounded-full" src="<?php echo $associationProfilePicture ?>" alt="">
                    <h1 class="text-xl"><?php echo $associationName ?></h1>
                </div>
                <p><?php echo $associationDesc ?></p>
            </div>
            <div class="rightAssocaitionContainer p-10 flex flex-col gap-y-5">
                <h2 class="text-5xl">NOM DE LA MISSION</h2>
                <div class="associationInformationContainer flex flex-col gap-y-5">
                    <p><?php echo $associationMission ?></p>
                </div>
                <hr>
                <div class="associationLocation flex gap-x-5">
                    <div class="imageLocationContainer flex flex-col rounded-2xl shadow-lg w-1/2 h-[300px]">
                        <img src="" alt="">
                        <div id="map" style="height: 400px; width: 100%;" class="z-0"></div> 
                    </div>
                    <div class="buttonPostulateContainer w-1/2">
                        <form id="postulationForm">
                            <input type="hidden" name="association_id" value="<?php echo $associationId; ?>">
                            <button type="submit" id="postulerBtn"
                                class="py-2 px-5 bg-blue-100 rounded-3xl cursor-pointer hover:bg-blue-200">
                                Postuler
                            </button>
                        </form>
                        <p class="text-sm text-slate-400">En vous inscrivant à cette association, vous acceptez de
                            respecter les conditions suivantes : participer activement aux missions, respecter les
                            autres membres et suivre les directives de l'association. Merci pour votre engagement.</p>
                    </div>
                </div>
                <hr>
            </div>
        </div>
        <div class="bottomAssociationContainer">
            <div class="disponibilityContainer">

            </div>
        </div>
    </div>
    <!-- Pop-up -->
    <div id="popup" class="fixed inset-0 flex items-center justify-center bg-black/50 bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-80 text-center">
            <div id="loading" class="flex items-center justify-center">
                <svg class="animate-spin h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span class="ml-2">Inscription en cours...</span>
            </div>
            <div id="confirmation" class="hidden">
                <p id="confirmationMessage" class="font-bold"></p>
                <button id="closePop"
                    class="py-2 px-5 bg-blue-100 rounded-3xl cursor-pointer hover:bg-blue-200 my-2">Fermer</button>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Remplacer ou modifier le code qui gère la soumission du formulaire de postulation
// Remplacez le code du gestionnaire d'événement par celui-ci
document.getElementById('postulationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Afficher la popup de chargement
    const popup = document.getElementById('popup');
    const loading = document.getElementById('loading');
    const confirmation = document.getElementById('confirmation');
    const confirmationMessage = document.getElementById('confirmationMessage');
    
    // Afficher la popup avec l'animation de chargement
    popup.classList.remove('hidden');
    loading.classList.remove('hidden');
    confirmation.classList.add('hidden');
    
    fetch('postulate.php', {
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
                showXPNotification(data.xp_points, "Candidature envoyée!");
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
        confirmationMessage.textContent = "Une erreur est survenue lors de la postulation.";
        confirmationMessage.className = "font-bold text-red-600";
    });
});

// Ajouter le gestionnaire pour fermer la popup
document.getElementById('closePop').addEventListener('click', function() {
    document.getElementById('popup').classList.add('hidden');
    window.location.reload(); // Recharger la page après fermeture
});

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
    <script>
        let associationName = "<?php echo $associationName; ?>";
        document.addEventListener("DOMContentLoaded", function () {
            // Récupérer les coordonnées dynamiquement passées par PHP
            let associationData = <?php echo json_encode($decodedAddress, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;

            if (associationData && associationData.coordinates && associationData.coordinates.length === 2) {
                let lat = parseFloat(associationData.coordinates[1]); // Latitude
                let lon = parseFloat(associationData.coordinates[0]); // Longitude
                
                // Initialisation de la carte Leaflet
                let map = L.map('map').setView([lat, lon], 13); // Zoom par défaut

                // Ajouter la couche OpenStreetMap
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                // Ajouter un marqueur à la carte
                L.marker([lat, lon]).addTo(map)
                    .bindPopup('<b>' + associationName + '</b>')  // Affiche le nom de l'association
                    .openPopup();
            } else {
                console.error("Erreur : Coordonnées invalides !");
            }
        });
    </script>

</body>

</html>