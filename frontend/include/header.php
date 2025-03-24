<?php
session_start();
include '../backend/db.php';
?>
<head>
<script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>

<header class="bg-pink-300 p-5">
    <nav class="flex flex-row justify-between">
        <div class="leftheader">
            <!-- Logo -->
        </div>
        <div class="rightheader flex flex-row gap-x-10">
            <div class="searchbar flex items-center relative">
                <!-- Barre de recherche -->
                <input id="searchInput" class="px-4 py-2 border rounded-full w-[400px] h-2/3 focus:outline-none focus:ring-2 focus:ring-pink-500 bg-slate-100" type="text" placeholder="Rechercher">
                <!-- Zone de résultats de la recherche -->
                <div id="searchResults" class="absolute w-full bg-white shadow-lg mt-2 rounded-lg max-h-60 overflow-y-auto"></div>
            </div>
            <a class="profilepicture w-[75px] pointer" href="profile.php">
                <img class="rounded-full" src="<?php echo $_SESSION['user_profile_picture']?>" alt="Image de <?php echo $_SESSION['firstname']?>">
            </a>
        </div>
    </nav>
</header>
<div id="searchPopup" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center hidden z-2">
    <div class="bg-white rounded-lg p-6 w-[90%] md:w-[600px]">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Résultats de recherche</h2>
            <button id="closePopup" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <div id="associationList" class="space-y-4">
            <!-- Les résultats de recherche seront affichés ici -->
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("searchInput");
        const searchPopup = document.getElementById("searchPopup");
        const closePopup = document.getElementById("closePopup");
        const associationList = document.getElementById("associationList");

        searchInput.addEventListener("input", function () {
            const query = searchInput.value;

            if (query.length >= 3) {  // Se déclenche si l'utilisateur tape au moins 3 caractères
                fetch(`search_associations.php?q=${query}`)
                    .then(response => response.json())  // Convertir la réponse en JSON
                    .then(data => {
                        // Vérifie si on a des résultats
                        if (Array.isArray(data) && data.length > 0) {
                            associationList.innerHTML = "";  // Vider la liste avant de rajouter de nouveaux résultats

                            data.forEach(association => {
    // Crée un div pour chaque association
    let div = document.createElement('div');
    div.classList.add('association-item');
    div.classList.add('border-b', 'pb-2', 'mb-2');

    // Crée un lien <a> qui englobe la carte entière
    let link = document.createElement('a');
    
    // Lien pour envoyer l'ID de l'association à set_session.php
    link.href = "#";  // Pas besoin de spécifier une URL ici, on va utiliser un événement de clic

    // Crée le contenu de l'élément div
    div.innerHTML = `
        <div class="association-card bg-gray-100 p-4">
            <h3 class="text-lg font-semibold">${association["association_name"]}</h3>
            <p>${association["association_desc"]}</p>
            <p>Email: ${association["association_mail"]}</p>
            <p>Mission: ${association["association_mission"]}</p>
            <p><a href="mailto:${association["association_mail"]}" class="text-pink-500">Contact</a></p>
        </div>
    `;

    // Ajoute l'élément div dans le lien
    link.appendChild(div);

    // Ajoute un événement au clic pour envoyer l'ID de l'association à set_session.php
    link.addEventListener('click', function () {
        // Envoie une requête POST à set_session.php pour stocker l'ID dans la session
        fetch('set_session.php', {
            method: 'POST',
            body: new URLSearchParams({
                'association_id': association['association_id']  // Envoie l'ID de l'association
            })
        })
        .then(response => response.text()) // On peut gérer la réponse si nécessaire
        .then(() => {
            // Redirige vers la page association.php après avoir enregistré l'ID dans la session
            window.location.href = 'association.php'; // Redirige vers association.php
        })
        .catch(error => {
            console.error('Erreur lors de l\'envoi de l\'ID:', error);
        });
    });

    // Ajoute le lien dans le conteneur de la liste des associations
    associationList.appendChild(link);
});




                            // Afficher le pop-up
                            searchPopup.classList.remove('hidden');
                        } else {
                            console.log("Aucune association trouvée.");
                            associationList.innerHTML = "<p>Aucune association trouvée.</p>";
                            searchPopup.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error("Erreur lors de la récupération des données :", error);
                    });
            } else {
                // Cacher le pop-up si moins de 3 caractères sont tapés
                searchPopup.classList.add('hidden');
                associationList.innerHTML = "";
            }
        });

        // Fermeture du pop-up
        closePopup.addEventListener("click", function() {
            searchPopup.classList.add('hidden');
        });
    });
</script>



