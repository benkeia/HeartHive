<?php

session_start();

include '../backend/db.php';

// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user_id']);
?>

<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['type']) && $_SESSION['type'] == 1): ?>
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
        /* Styles pour la barre de défilement personnalisée */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #ccc;
        }
        
        /* Style pour limiter le texte à 2 lignes */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        /* Ajouter les autres styles ici */
    </style>
</head>

<!-- Reste du code identique -->

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
        <?php if ($isLoggedIn): ?>
        <!-- Icône de notification -->
        <button class="relative">
            <svg class="w-8 h-8 text-gray-600 hover:text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
        </button>

        <!-- Photo de profil avec popover pour utilisateur connecté -->
        <div class="relative group">
            <a href="#" class="block">
                <img src="<?php echo $_SESSION['user_profile_picture'] ?? 'assets/uploads/profile_pictures/default.webp'; ?>" alt="Profil"
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
                    <a href="messages.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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
                    <?php if (isset($_SESSION['type']) && $_SESSION['type'] != 1): ?>
                        <a href="certifications.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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
        <?php else: ?>
        <!-- Options pour utilisateur non connecté -->
        <div class="flex items-center space-x-2">
            <a href="loginPage.php" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                Connexion
            </a>
            <a href="signup.php" class="px-4 py-2 text-sm font-medium text-white bg-pink-500 hover:bg-pink-600 rounded-lg transition-colors">
                S'inscrire
            </a>
            
            <!-- Affichage "Invité" -->
            <div class="flex items-center ml-2 text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-sm font-medium">Invité</span>
            </div>
        </div>
        <?php endif; ?>
    </div>
</header>

<div class="h-[50px]"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($isLoggedIn): ?>
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
        <?php endif; ?>
        
        // Reste de votre JavaScript existant...
    });
</script>

<!-- Pop-up de recherche améliorée -->
<div id="searchPopup" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex justify-center items-center hidden z-50 transition-all duration-300">
  <div class="bg-white rounded-2xl shadow-2xl p-6 w-[90%] md:w-[650px] max-h-[80vh] transform transition-all duration-300 scale-95 opacity-0" id="searchPopupContent">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-pink-500">
          <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
        Résultats de recherche
      </h2>
      <button id="closePopup" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-full hover:bg-gray-100">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    
    <!-- Barre de recherche intégrée -->
    <div class="relative mb-6">
      <input type="text" id="popupSearchInput" class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-400 focus:border-pink-400 outline-none transition-all shadow-sm" placeholder="Affinez votre recherche..." />
      <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
      </svg>
    </div>
    
    <!-- Filtres rapides -->
    <div class="flex flex-wrap gap-2 mb-6">
      <span class="text-sm text-gray-500 self-center mr-1">Filtres :</span>
      <button class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 hover:bg-pink-100 text-gray-700 hover:text-pink-700 transition-colors">Associations</button>
      <button class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 hover:bg-pink-100 text-gray-700 hover:text-pink-700 transition-colors">Missions</button>
      <button class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 hover:bg-pink-100 text-gray-700 hover:text-pink-700 transition-colors">Populaires</button>
      <button class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 hover:bg-pink-100 text-gray-700 hover:text-pink-700 transition-colors">Près de moi</button>
    </div>
    
    <div class="overflow-y-auto max-h-[50vh] pr-1 custom-scrollbar">
      <div id="associationList" class="space-y-3">
        <!-- Les résultats de recherche seront affichés ici -->
        <div class="text-center py-16 text-gray-500">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto text-gray-300 mb-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
          </svg>
          <p class="text-lg font-medium">Commencez à taper pour rechercher</p>
          <p class="text-sm mt-1">Trouvez des associations et des missions qui vous correspondent</p>
        </div>
      </div>
    </div>
    
    <div class="mt-6 pt-4 border-t border-gray-100 flex justify-between items-center">
      <p class="text-sm text-gray-500"><span id="resultCount">0</span> résultats trouvés</p>
      <a href="#" class="text-sm font-medium text-pink-600 hover:text-pink-800 transition-colors">Voir tous les résultats</a>
    </div>
  </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("searchInput");
    const popupSearchInput = document.getElementById("popupSearchInput");
    const searchPopup = document.getElementById("searchPopup");
    const searchPopupContent = document.getElementById("searchPopupContent");
    const closePopup = document.getElementById("closePopup");
    const associationList = document.getElementById("associationList");
    const resultCount = document.getElementById("resultCount");

    let searchTimeout;

    // Synchroniser les deux champs de recherche
    searchInput.addEventListener("input", function() {
      if (popupSearchInput) {
        popupSearchInput.value = searchInput.value;
      }
      handleSearch(searchInput.value);
    });

    if (popupSearchInput) {
      popupSearchInput.addEventListener("input", function() {
        searchInput.value = popupSearchInput.value;
        handleSearch(popupSearchInput.value);
      });
    }

    function handleSearch(query) {
      clearTimeout(searchTimeout);

      searchTimeout = setTimeout(() => {
        if (query.length >= 2) {
          fetch(`search_associations.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
              if (Array.isArray(data) && data.length > 0) {
                associationList.innerHTML = "";
                resultCount.textContent = data.length;
                
                data.forEach(association => {
                  const div = document.createElement('div');
                  div.classList.add('association-item', 'hover:scale-[1.02]', 'transition-transform', 'duration-200');
                  
                  // Créer une description courte
                  const shortDesc = association.association_desc ? 
                    association.association_desc.length > 100 ? 
                    association.association_desc.substring(0, 100) + '...' : 
                    association.association_desc : 
                    'Aucune description disponible';
                  
                  // Image par défaut si l'association n'en a pas
                  const logo = association.association_logo || 'assets/uploads/profile_pictures/default.webp';
                  
                  div.innerHTML = `
                    <div class="association-card bg-white p-4 hover:bg-gray-50 cursor-pointer rounded-xl border border-gray-200 shadow-sm flex gap-4">
                      <img src="${logo}" alt="${association.association_name}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                      <div class="flex-grow">
                        <h3 class="text-lg font-semibold text-gray-800">${association.association_name}</h3>
                        <p class="text-sm text-gray-600 line-clamp-2 mb-2">${shortDesc}</p>
                        <div class="flex flex-wrap gap-1">
                          <span class="text-xs bg-pink-100 text-pink-800 px-2 py-0.5 rounded-full">${association.mission_count || 0} mission(s)</span>
                          <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">Association</span>
                        </div>
                      </div>
                      <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                      </div>
                    </div>
                  `;

                  div.addEventListener('click', () => {
                    window.location.href = "association.php?id=" + association.association_id;
                  });

                  associationList.appendChild(div);
                });
                
                openSearchPopup();
              } else {
                associationList.innerHTML = `
                  <div class="text-center py-12 px-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto text-gray-300 mb-3">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
                    </svg>
                    <p class="text-lg font-medium text-gray-700">Aucune association trouvée</p>
                    <p class="text-sm text-gray-500 mt-1">Essayez d'autres termes de recherche</p>
                  </div>
                `;
                resultCount.textContent = "0";
                openSearchPopup();
              }
            })
            .catch(error => {
              console.error("Erreur:", error);
            });
        }
      }, 300); // Délai de 300ms pour éviter trop de requêtes
    }

    function openSearchPopup() {
      searchPopup.classList.remove('hidden');
      // Animation d'entrée
      setTimeout(() => {
        searchPopupContent.classList.remove('scale-95', 'opacity-0');
        searchPopupContent.classList.add('scale-100', 'opacity-100');
      }, 10);
    }

    function closeSearchPopup() {
      // Animation de sortie
      searchPopupContent.classList.remove('scale-100', 'opacity-100');
      searchPopupContent.classList.add('scale-95', 'opacity-0');
      setTimeout(() => {
        searchPopup.classList.add('hidden');
      }, 300);
    }

    closePopup.addEventListener("click", function() {
      closeSearchPopup();
    });

    // Fermer avec Echap
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeSearchPopup();
      }
    });

    // Fermer en cliquant en dehors
    searchPopup.addEventListener('click', function(e) {
      if (e.target === searchPopup) {
        closeSearchPopup();
      }
    });
  });
</script>