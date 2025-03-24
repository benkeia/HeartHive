<?php

session_start();
include '../backend/db.php';

// Vérification du type de session
if (isset($_SESSION['type']) && $_SESSION['type'] == 0) {
  header('Location: profile.php');
  exit;
}

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
  header('Location: loginPage.php');
  exit;
}

// Récupérer les tags de l'utilisateur s'ils existent
$userTags = isset($_SESSION['user_tags']) ? $_SESSION['user_tags'] : '{}';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'primary': '#8B5CF6',
            'primary-dark': '#7C3AED',
            'secondary': '#EC4899',
            'light-bg': '#F9FAFB',
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          },
          boxShadow: {
            'custom': '0 4px 15px rgba(0, 0, 0, 0.05)',
          }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
  <script src="js/codeProfile.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
  <title>Profile</title>
  <style>
    body {
      background-color: #F9FAFB;
      font-family: 'Inter', sans-serif;
    }

    .section-title {
      position: relative;
      margin-bottom: 1.5rem;
      font-weight: 600;
      color: #4B5563;
      padding-bottom: 0.5rem;
    }

    .section-title:after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      height: 3px;
      width: 40px;
      background: linear-gradient(to right, #8B5CF6, #EC4899);
      border-radius: 3px;
    }

    .menu-item {
      transition: all 0.2s ease;
      border-left: 3px solid transparent;
    }

    .menu-item:hover,
    .menu-item.active {
      border-left: 3px solid #8B5CF6;
      background-color: #F3F4F6;
      color: #8B5CF6;
      font-weight: 500;
    }

    .gradient-btn {
      background: #FFE9EF;
      border: 2px solid #CF3275;
      border-radius: 6px;
      color: #CF3275;
      font-weight: 500;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 10px 24px;
      transition: all 0.2s ease;
    }

    .gradient-btn:hover {
      background: #ffd6e2;
      box-shadow: 0 4px 12px rgba(207, 50, 117, 0.15);
      transform: translateY(-1px);
    }

    .gradient-btn svg {
      stroke: #CF3275;
    }

    .round-btn {
      background: #FFE9EF;
      border: 2px solid #CF3275;
      color: #CF3275;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      transition: all 0.2s ease;
    }

    .round-btn:hover {
      background: #ffd6e2;
      box-shadow: 0 4px 12px rgba(207, 50, 117, 0.15);
    }

    .round-btn svg {
      stroke: #CF3275;
    }
  </style>
</head>

<body class="bg-light-bg font-sans">

  <?php include 'include/header.php'; ?>

  <!-- Ajout des données de tags pour JS -->
  <div id="userTagsData" data-tags='<?php echo htmlspecialchars($userTags, ENT_QUOTES, 'UTF-8'); ?>' class="hidden"></div>


  <div class="flex flex-col md:flex-row gap-6">
    <!-- Menu latéral -->
    <div class="w-full md:w-1/4 bg-white rounded-xl shadow-custom overflow-hidden">
      <div class="p-5">
        <h3 class="text-xl font-bold mb-6 text-gray-800">Compte de l'association</h3>
        <ul class="space-y-1">
          <li class="menu-item active p-3 rounded-lg text-gray-700">informations</li>
          <li class="menu-item p-3 rounded-lg text-gray-700">Messagerie</li>
          <li class="menu-item p-3 rounded-lg text-gray-700">Paramètres</li>
        </ul>
      </div>
    </div>

    <!-- Contenu principal -->
    <div class="w-full md:w-3/4">
      <div class="bg-white rounded-xl shadow-custom p-6">
        <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
          <div class="relative">
            <img id="profilePic" src="<?php echo $_SESSION['user_profile_picture'] ?>" alt="Photo de profil" class="h-28 w-28 rounded-full border-4 border-purple-100 object-cover shadow-md" />
            <div class="absolute -right-2 -bottom-1 bg-white p-1 rounded-full shadow-sm">
              <div class="p-1 rounded-full bg-gradient-to-r from-primary to-secondary cursor-pointer">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="flex-1">
            <h2 id="profileName" class="text-2xl font-bold text-gray-800 mb-1"><?php echo $_SESSION['firstname'] ?></h2>
            <h4 id="profileLocation" class="flex items-center text-gray-600 mb-3 font-medium">
              <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0z"></path>
              </svg>
              <?php
              if (isset($_SESSION['user_adress']) && $_SESSION['user_adress'] !== '') {
                $address = json_decode($_SESSION['user_adress'], true);
                echo isset($address['name']) ? $address['name'] : 'Aucune adresse renseignée';
              } else {
                echo 'Aucune adresse renseignée';
              }
              ?>
            </h4>
            <p id="profileBio" class="text-gray-600 text-sm leading-relaxed border-l-4 border-purple-200 pl-4 italic">
              <?php if (isset($_SESSION['user_bio'])) {
                echo $_SESSION['user_bio'];
              } else {
                echo "Aucune biographie renseignée";
              } ?>
            </p>
          </div>
        </div>

        <!-- Bouton Modifier -->
        <button id="editProfileBtn" class="mt-6 gradient-btn">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
          </svg>
          Modifier mon profil
        </button>

        <!-- Formulaire de modification caché -->
        <div id="editProfileForm" class="hidden mt-8 bg-gray-50 p-6 rounded-lg border border-gray-100">
          <h3 class="section-title">Modifier le profil</h3>
          <form id="profileForm" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="name" class="block text-gray-700 font-medium mb-2">Nom Prénom</label>
                <input type="text" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 focus:border-transparent transition" id="name" value="Nom Prénom" />
              </div>
              <div>
                <label for="location" class="block text-gray-700 font-medium mb-2">Localisation</label>
                <input type="text" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 focus:border-transparent transition" id="location" value="Location" />
              </div>
            </div>
            <div>
              <label for="bio" class="block text-gray-700 font-medium mb-2">Biographie</label>
              <textarea class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 focus:border-transparent transition" id="bio" rows="3">Biographie</textarea>
            </div>
            <div>
              <label for="profilePicInput" class="block text-gray-700 font-medium mb-2">Photo de Profil</label>
              <input type="file" class="w-full bg-white border border-gray-300 rounded-lg p-3" id="profilePicInput" />
            </div>
            <div class="flex gap-3 mt-6">
              <button type="submit" class="gradient-btn">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Enregistrer
              </button>
              <button type="button" id="cancelEdit" class="bg-gray-100 text-gray-700 border border-gray-300 py-2.5 px-6 rounded-lg font-medium hover:bg-gray-200 transition">Annuler</button>
            </div>
          </form>
        </div>

        <!-- Correction de la structure pour les blocs de contenu -->
        <div class="flex flex-col lg:flex-row mt-12 gap-8">
          <div class="w-full lg:w-1/2">
            <div class="mb-8">
              <h2 class="section-title text-lg">Domaines d'activités de l'association</h2>
              <div id="interestsContainer" class="flex flex-wrap gap-2 mt-4">
                <!-- Boutons cochables apparaissent ici -->
              </div>

              <div class="tag-input-container mt-5">
                <div class="search-wrapper">
                  <input type="text" id="interestSearch" class="tag-search" placeholder="Rechercher un intérêt...">
                  <button id="addInterestBtn" class="add-tag-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                  </button>
                </div>
                <div id="interestMenu" class="hidden tag-menu">
                  <ul id="interestList" class="tag-list">
                    <!-- Liste des intérêts possibles -->
                  </ul>
                </div>
              </div>
            </div>


            <style>
              .tag {
                display: inline-flex;
                align-items: center;
                background-image: linear-gradient(to right, #fdf5f9, #ffe9ef);
                color: #CF3275;
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

              .tag svg {
                margin-left: 0.5rem;
                width: 14px;
                height: 14px;
                cursor: pointer;
                stroke: #CF3275;
                transition: transform 0.2s ease;
              }

              .tag svg:hover {
                transform: rotate(90deg);
              }

              .tag-input-container {
                position: relative;
                width: 100%;
              }

              .search-wrapper {
                display: flex;
                align-items: center;
                width: 100%;
                border: 1px solid #E5E7EB;
                border-radius: 8px;
                overflow: hidden;
                transition: all 0.3s ease;
                background-color: white;
              }

              .search-wrapper:focus-within {
                border-color: #CF3275;
                box-shadow: 0 0 0 2px rgba(207, 50, 117, 0.1);
              }

              .tag-search {
                flex-grow: 1;
                border: none;
                padding: 0.7rem 1rem;
                font-size: 0.9rem;
                outline: none;
                background: transparent;
              }

              .add-tag-btn {
                background-color: #FFE9EF;
                color: #CF3275;
                border: none;
                height: 100%;
                padding: 0.7rem;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background-color 0.2s ease;
              }

              .add-tag-btn:hover {
                background-color: #ffd6e2;
              }

              .tag-menu {
                position: absolute;
                width: 100%;
                max-height: 200px;
                overflow-y: auto;
                background: white;
                border: 1px solid #E5E7EB;
                border-top: none;
                border-radius: 0 0 8px 8px;
                z-index: 10;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
              }

              .tag-list {
                list-style: none;
                padding: 0;
                margin: 0;
              }

              .tag-list li {
                padding: 0.75rem 1rem;
                cursor: pointer;
                transition: background-color 0.2s ease;
                display: flex;
                align-items: center;
              }

              .tag-list li:hover {
                background-color: #F9FAFB;
              }

              .tag-list li.selected {
                background-color: #FFE9EF;
                color: #CF3275;
              }
            </style>
          </div>


        </div>
      </div>
    </div>
  </div>


  <script>
    // Activer le premier élément du menu par défaut
    document.querySelector('.menu-item').classList.add('active');
  </script>

</body>

</html>