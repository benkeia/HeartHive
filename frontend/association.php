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
    <link rel="stylesheet" href="frontend/assets/css/style.css">
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
        $associationAdress = htmlspecialchars($association['association_adress']);
        $associationDesc = htmlspecialchars($association['association_desc']);
        $associationMission = htmlspecialchars($association['association_mission']);
    }

    // Fermer la requête
    $associationStatement->close();
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
                    <div class="imageLocationContainer flex flex-col rounded-2xl shadow-lg">
                        <img src="" alt="">
                        <p><?php echo $associationAdress ?></p>
                    <div id="map" style="height: 400px; width: 100%;"></div>
                    <script>
                        function initMap() {
                            const geocoder = new google.maps.Geocoder();
                            const address = "<?php echo $associationAdress; ?>";

                            geocoder.geocode({ 'address': address }, function (results, status) {
                                if (status === 'OK') {
                                    const map = new google.maps.Map(document.getElementById('map'), {
                                        zoom: 15,
                                        center: results[0].geometry.location
                                    });
                                    new google.maps.Marker({
                                        map: map,
                                        position: results[0].geometry.location
                                    });
                                } else {
                                    console.error('Geocode was not successful for the following reason: ' + status);
                                }
                            });
                        }
                    </script>
                    <script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap"></script>
                    </div>
                    <div class="buttonPostulateContainer">
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
    <script>
        document.getElementById('postulationForm').addEventListener('submit', function (event) {
            event.preventDefault(); // Empêche le rechargement de la page

            const popup = document.getElementById('popup');
            const loading = document.getElementById('loading');
            const confirmation = document.getElementById('confirmation');
            const confirmationMessage = document.getElementById('confirmationMessage');
            const formData = new FormData(this);
            const closePop = document.getElementById('closePop');

            // Affiche la pop-up avec le chargement
            popup.classList.remove('hidden');
            loading.classList.remove('hidden');
            confirmation.classList.add('hidden');

            closePop.addEventListener('click', () => {
                popup.classList.add('hidden');
            });

            fetch('postulate.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json()) // Convertit en JSON
                .then(data => {
                    loading.classList.add('hidden');
                    confirmation.classList.remove('hidden');

                    // Affiche le message de retour du serveur
                    confirmationMessage.textContent = data.message;
                    confirmationMessage.classList.remove("text-green-600", "text-red-600");

                    if (data.status === "success") {
                        confirmationMessage.classList.add("text-green-600");
                    } else {
                        confirmationMessage.classList.add("text-red-600");


                    }

                    // Ferme la pop-up après 3 secondes
                    setTimeout(() => {
                        popup.classList.add('hidden');
                    }, 3000);
                })
                .catch(error => {
                    loading.classList.add('hidden');
                    confirmation.classList.remove('hidden');
                    confirmationMessage.textContent = "Erreur réseau. Réessayez.";
                    confirmationMessage.classList.add("text-red-600");

                    setTimeout(() => {
                        popup.classList.add('hidden');
                    }, 3000);
                });
        });
    </script>
</body>

</html>