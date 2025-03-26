<?php

session_start();

include '../backend/db.php';
?>



<head>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($_SESSION['type'] == 1): ?>
                document.querySelector('header').classList.remove('bg-custom-pink');
                document.querySelector('header').style.backgroundColor = '#FB8E00';
            <?php endif; ?>
        });
    </script>
    <style>
        @layer utilities {
            .bg-custom-pink {
                background-color: #ffb3e4;
            }
        }
    </style>
    <style>
        .xp-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(to right, #8B5CF6, #EC4899);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            display: flex;
            align-items: center;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .xp-notification.show {
            transform: translateY(0);
            opacity: 1;
        }

        /* Ajouter les autres styles ici */
    </style>
</head>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const popover = document.getElementById('popover');
        let timeout;

        document.querySelector('.group').addEventListener('mouseenter', function() {
            clearTimeout(timeout);
            popover.classList.remove('hidden');
            popover.classList.add('opacity-100');
        });

        document.querySelector('.group').addEventListener('mouseleave', function() {
            timeout = setTimeout(function() {
                popover.classList.add('hidden');
                popover.classList.remove('opacity-100');
            }, 100);
        });
    });
</script>

<head>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($_SESSION['type'] == 1): ?>
                document.querySelector('header').classList.remove('bg-custom-pink');
                document.querySelector('header').style.backgroundColor = '#FB8E00';
            <?php endif; ?>
        });
    </script>
    <style>
        @layer utilities {
            .bg-custom-pink {
                background-color: #ffb3e4;
            }
        }
    </style>
</head>


<header class="bg-custom-pink shadow-md p-4 flex items-center justify-between absolute top-0 left-0 w-full z-10">
    <!-- Logo -->
    <a href="index.php">
        <img src="assets/img/LogoHeader.png" class="h-12 ml-4" alt="">
    </a>

    <!-- Barre de recherche -->
    <div class="relative w-1/3">
        <input id="searchInput" type="text" placeholder="Rechercher..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
        <!-- Pop-up des résultats -->
        <div id="searchResults"
            class="absolute w-full bg-white shadow-lg mt-2 rounded-lg max-h-60 overflow-y-auto z-50"></div>
    </div>

    <!-- Icônes à droite -->
    <div class="flex items-center space-x-6">
        <!-- Icône de notification -->
        <button class="relative">
            <svg class="w-8 h-8 text-gray-600 hover:text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
        </button>

        <!-- Photo de profil avec popover -->
        <div class="relative group">
            <a href="#" class="block">
                <img src="<?php echo $_SESSION['user_profile_picture'] ?>" alt="Profil"
                    class="w-12 h-12 rounded-full border border-gray-300" />
            </a>

            <!-- Popover au hover -->
            <div id="popover"
                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden transition-opacity duration-300 opacity-0">
                <div class="py-1">
                    <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Voir le profil
                        </div>
                    </a>
                    <a href="profile.php?tab=messages" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="CurrentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            Messages
                        </div>
                    </a>
                    <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="CurrentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Paramètres
                        </div>
                    </a>
                    <?php if ($_SESSION['type'] != 1): ?>
                        <a href="profile.php?tab=certifications" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                    stroke="CurrentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                                Certifications
                            </div>
                        </a>
                    <?php endif; ?>
                    <hr class="my-1">
                    <a href="disconnect.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="CurrentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Déconnexion
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const popover = document.getElementById('popover');
                let timeout;

                document.querySelector('.group').addEventListener('mouseenter', function() {
                    clearTimeout(timeout);
                    popover.classList.remove('hidden');
                    popover.classList.add('opacity-100');
                });

                document.querySelector('.group').addEventListener('mouseleave', function() {
                    timeout = setTimeout(function() {
                        popover.classList.add('hidden');
                        popover.classList.remove('opacity-100');
                    }, 100);
                });
            });
        </script>
    </div>
    </div>
</header>
<div class="h-[50px]"></div>

<div id="searchPopup" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center hidden z-50">
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
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchInput");
        const searchPopup = document.getElementById("searchPopup");
        const closePopup = document.getElementById("closePopup");
        const associationList = document.getElementById("associationList");

        let searchTimeout;

        searchInput.addEventListener("input", function() {
            clearTimeout(searchTimeout);
            const query = searchInput.value;

            searchTimeout = setTimeout(() => {
                if (query.length >= 3) {
                    fetch(`search_associations.php?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (Array.isArray(data) && data.length > 0) {
                                associationList.innerHTML = "";
                                data.forEach(association => {
                                    const div = document.createElement('div');
                                    div.classList.add('association-item', 'border-b', 'pb-2', 'mb-2');
                                    div.innerHTML = `
                                        <div class="association-card bg-gray-100 p-4 hover:bg-gray-200 cursor-pointer rounded">
                                            <h3 class="text-lg font-semibold">${association.association_name}</h3>
                                            <p class="text-sm text-gray-600">${association.association_desc}</p>
                                            <p class="text-sm mt-2">Mission: ${association.association_mission}</p>
                                        </div>
                                    `;

                                    div.addEventListener('click', () => {
                                        // Utiliser la même méthode que dans index.php
                                        fetch("set_session.php", {
                                                method: "POST",
                                                headers: {
                                                    "Content-Type": "application/x-www-form-urlencoded"
                                                },
                                                body: "association_id=" + association.association_id
                                            })
                                            .then(response => response.text())
                                            .then(() => {
                                                window.location.href = "association.php"; // Redirection après stockage
                                            })
                                            .catch(error => console.error("Erreur lors de l'envoi de l'ID :", error));
                                    });

                                    associationList.appendChild(div);
                                });
                                searchPopup.classList.remove('hidden');
                            } else {
                                associationList.innerHTML = "<p class='text-center py-4'>Aucune association trouvée.</p>";
                                searchPopup.classList.remove('hidden');
                            }
                        })
                        .catch(error => {
                            console.error("Erreur:", error);
                        });
                } else {
                    searchPopup.classList.add('hidden');
                }
            }, 300); // Délai de 300ms pour éviter trop de requêtes
        });

        closePopup.addEventListener("click", function() {
            searchPopup.classList.add('hidden');
        });

        // Fermer avec Echap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                searchPopup.classList.add('hidden');
            }
        });

        // Fermer en cliquant en dehors
        searchPopup.addEventListener('click', function(e) {
            if (e.target === searchPopup) {
                searchPopup.classList.add('hidden');
            }
        });
    });
</script>