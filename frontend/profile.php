<?php

session_start();
include '../backend/db.php';


if (isset($_SESSION['type']) && $_SESSION['type'] == 1) {
  header('Location: profile_asso.php');
  exit;
}

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
  header('Location: loginPage.php');
  exit;
}

// Récupérer les tags de l'utilisateur s'ils existent
$userTags = isset($_SESSION['user_tags']) ? $_SESSION['user_tags'] : '{}';

function auto_update_xp_level() {
  global $conn;
  
  if (!isset($_SESSION['user_id'])) {
      return false;
  }
  
  $user_id = $_SESSION['user_id'];
  
  // Récupérer les points totaux de l'utilisateur
  $stmt = $conn->prepare("SELECT * FROM user_experience WHERE user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($result->num_rows > 0) {
      $xp_data = $result->fetch_assoc();
      $total_points = $xp_data['total_points'];
      
      // Définir les seuils de niveaux
      $levels = [
          1 => 0,
          2 => 100,
          3 => 250,
          4 => 500,
          5 => 1000,
          6 => 2000,
          7 => 3500,
          8 => 5000,
          9 => 7500,
          10 => 10000
      ];
      
      // Calculer le niveau correct en fonction des points totaux
      $correct_level = 1;
      foreach ($levels as $level => $threshold) {
          if ($total_points >= $threshold) {
              $correct_level = $level;
          } else {
              break;
          }
      }
      
      // Calculer les points requis pour le niveau suivant
      $next_level = $correct_level + 1;
      $next_level_points = isset($levels[$next_level]) ? $levels[$next_level] : 999999;
      
      // Si le niveau est incorrect, mettre à jour
      if ($correct_level != $xp_data['current_level'] || $next_level_points != $xp_data['points_to_next_level']) {
          $update = $conn->prepare("UPDATE user_experience SET current_level = ?, points_to_next_level = ? WHERE user_id = ?");
          $update->bind_param("iii", $correct_level, $next_level_points, $user_id);
          $update->execute();
          
          // Retourner les nouvelles valeurs
          return [
              'updated' => true,
              'new_level' => $correct_level,
              'old_level' => $xp_data['current_level'],
              'points_to_next_level' => $next_level_points
          ];
      }
  }
  
  return ['updated' => false];
}

// Exécuter la mise à jour automatique à chaque chargement de la page
$level_update = auto_update_xp_level();

// Si le niveau a été mis à jour, afficher une notification
if (isset($level_update['updated']) && $level_update['updated']) {
  // Stocker l'information dans une variable pour l'utiliser dans le JavaScript plus tard
  $show_level_update = true;
  $new_level = $level_update['new_level'];
  $old_level = $level_update['old_level'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Cropper.js CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
  <!-- Cropper.js JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
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

    /* Styles pour la popup de recadrage */
    #cropperModal {
      backdrop-filter: blur(3px);
      transition: opacity 0.3s ease;
    }

    #cropperModal>div {
      max-height: 90vh;
      transition: transform 0.3s ease;
    }

    .cropper-container {
      margin: 0 auto;
    }

    /* Animation d'entrée et de sortie */
    #cropperModal.hidden {
      opacity: 0;
      pointer-events: none;
    }

    #cropperModal.hidden>div {
      transform: translateY(20px);
    }

    /* Style pour la navigation par onglets */
    .tab-content {
      display: none;
      animation: fadeIn 0.3s ease-in-out;
    }

    .tab-content.active {
      display: block;
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
    @keyframes pulse-slow {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.05); opacity: 0.7; }
  }
  .animate-pulse-slow {
    animation: pulse-slow 3s ease-in-out infinite;
  }
  
  @keyframes spin-slow {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  .animate-spin-slow {
    animation: spin-slow 15s linear infinite;
  }

    /* Style pour la liste des associations */
    #associationsList .gradient-btn {
      font-size: 0.875rem;
      padding: 0.5rem 1rem;
    }

    .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  </style>
</head>

<body class="bg-light-bg font-sans">

  <?php include 'include/header.php'; ?>

  <!-- Ajout des données de tags pour JS -->
  <div id="userTagsData" data-tags='<?php echo htmlspecialchars($userTags, ENT_QUOTES, 'UTF-8'); ?>' class="hidden">
  </div>


  <div class="flex flex-col md:flex-row gap-6">
    <!-- Menu latéral avec gestionnaires d'événements -->
    <div class="w-full md:w-1/4 bg-white rounded-xl shadow-custom overflow-hidden">
      <div class="p-5">
        <h3 class="text-xl font-bold mb-6 text-gray-800">Mon compte</h3>
        <ul class="space-y-1" id="profileTabs">
          <li data-tab="profile" class="menu-item active p-3 rounded-lg text-gray-700 cursor-pointer">Mon profil</li>
          <li data-tab="engagements" class="menu-item p-3 rounded-lg text-gray-700 cursor-pointer">Mes engagements</li>
          <li data-tab="stats" class="menu-item p-3 rounded-lg text-gray-700 cursor-pointer">Statistiques</li>
          <li data-tab="certifications" class="menu-item p-3 rounded-lg text-gray-700 cursor-pointer">Certifications
          </li>
          <li data-tab="messages" class="menu-item p-3 rounded-lg text-gray-700 cursor-pointer">Messagerie</li>
          <li data-tab="settings" class="menu-item p-3 rounded-lg text-gray-700 cursor-pointer">Paramètres</li>
        </ul>
      </div>
    </div>



    <!-- Contenu principal -->
    <div class="w-full md:w-3/4">



      <div id="profileTab" class="tab-content active">

        <div class="bg-white rounded-xl shadow-custom p-6">
          <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
            <div class="relative">
              <img id="profilePic" src="<?php echo $_SESSION['user_profile_picture'] ?>" alt="Photo de profil"
                class="h-28 w-28 rounded-full border-4 border-purple-100 object-cover shadow-md" />
              <div class="absolute -right-2 -bottom-1 bg-white p-1 rounded-full shadow-sm">
                <div id="editProfilePicIcon"
                  class="p-1 rounded-full bg-gradient-to-r from-primary to-secondary cursor-pointer">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                    </path>
                  </svg>
                </div>
              </div>
            </div>

            <div class="flex-1">
              <h2 id="profileName" class="text-2xl font-bold text-gray-800 mb-1">
                <?php echo $_SESSION['firstname'] . ' ' . $_SESSION['name']; ?>
              </h2>
              <h4 id="profileLocation" class="flex items-center text-gray-600 mb-3 font-medium">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                  xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
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
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
              </path>
            </svg>
            Modifier mon profil
          </button>

          <!-- Formulaire de modification caché -->
          <div id="editProfileForm" class="hidden mt-8 bg-gray-50 p-6 rounded-lg border border-gray-100">
            <h3 class="section-title">Modifier le profil</h3>
            <form id="profileForm" class="space-y-4" enctype="multipart/form-data">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="name" class="block text-gray-700 font-medium mb-2">Nom Prénom</label>
                  <input type="text"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 focus:border-transparent transition"
                    id="name" value="<?php echo $_SESSION['firstname'] . ' ' . $_SESSION['name']; ?>" />
                </div>
                <div>
                  <label for="location" class="block text-gray-700 font-medium mb-2">Localisation</label>
                  <div class="relative">
                    <input type="text"
                      class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 focus:border-transparent transition"
                      id="location" placeholder="Entrez une ville..." value="<?php
                      if (isset($_SESSION['user_adress']) && $_SESSION['user_adress'] !== '') {
                        $address = json_decode($_SESSION['user_adress'], true);
                        echo isset($address['name']) ? $address['name'] : '';
                      }
                      ?>" />
                    <div id="locationResults"
                      class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden">
                    </div>
                    <input type="hidden" id="locationCoordinates" />
                  </div>
                </div>
              </div>
              <div>
                <label for="locationRange" class="block text-gray-700 font-medium mb-2">Rayon (km)</label>
                <input type="range" min="1" max="100" step="1" value="<?php
                if (isset($_SESSION['user_adress']) && $_SESSION['user_adress'] !== '') {
                  $address = json_decode($_SESSION['user_adress'], true);
                  echo isset($address['range']) ? $address['range'] : '10';
                } else {
                  echo '10';
                }
                ?>" class="w-full" id="locationRange" />
                <div class="flex justify-between text-xs text-gray-500">
                  <span>1 km</span>
                  <span id="rangeValue">10 km</span>
                  <span>100 km</span>
                </div>
              </div>
              <div>
                <label for="bio" class="block text-gray-700 font-medium mb-2">Biographie</label>
                <textarea
                  class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-purple-300 focus:border-transparent transition"
                  id="bio" name="bio"
                  rows="3"><?php echo isset($_SESSION['user_bio']) ? $_SESSION['user_bio'] : ''; ?></textarea>
              </div>
              <div>
                <label for="profilePicInput" class="block text-gray-700 font-medium mb-2">Photo de Profil</label>
                <input type="file" class="hidden" id="profilePicInput" name="profile_picture" accept="image/*">

                <div class="flex items-center space-x-4 mb-4">
                  <div class="relative">
                    <img id="previewImage" src="<?php echo $_SESSION['user_profile_picture'] ?>"
                      class="w-24 h-24 rounded-full object-cover border-4 border-purple-100">
                  </div>
                  <button type="button" id="selectImageBtn" class="gradient-btn">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                      xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                      </path>
                    </svg>
                    Changer la photo
                  </button>
                </div>
                <input type="hidden" id="croppedImageData" name="cropped_image">
                <div class="flex gap-3 mt-6">
                  <button type="submit" class="gradient-btn">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                      xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Enregistrer
                  </button>
                  <button type="button" id="cancelEdit"
                    class="bg-gray-100 text-gray-700 border border-gray-300 py-2.5 px-6 rounded-lg font-medium hover:bg-gray-200 transition">Annuler</button>
                </div>
              </div>
          </div>
          </form>
        </div>

        <!-- Correction de la structure pour les blocs de contenu -->
        <div class="flex flex-col lg:flex-row mt-12 gap-8">
          <div class="w-full lg:w-1/2">
            <div class="mb-8">
              <h2 class="section-title text-lg">Mes centres d'intérêts</h2>
              <div id="interestsContainer" class="flex flex-wrap gap-2 mt-4">
                <!-- Boutons cochables apparaissent ici -->
              </div>

              <div class="tag-input-container mt-5">
                <div class="search-wrapper">
                  <input type="text" id="interestSearch" class="tag-search" placeholder="Rechercher un intérêt...">
                  <button id="addInterestBtn" class="add-tag-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                      xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
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

            <div>
              <h2 class="section-title text-lg">Mes compétences</h2>
              <div id="skillsContainer" class="flex flex-wrap gap-2 mt-4"></div>

              <div class="tag-input-container mt-5">
                <div class="search-wrapper">
                  <input type="text" id="skillSearch" class="tag-search" placeholder="Rechercher une compétence...">
                  <button id="addSkillBtn" class="add-tag-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                      xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                  </button>
                </div>
                <div id="skillMenu" class="hidden tag-menu">
                  <ul id="skillList" class="tag-list">
                    <!-- Liste des compétences possibles -->
                  </ul>
                </div>
              </div>
            </div>
            <!-- Popup de recadrage - version corrigée -->
            <div id="cropperModal"
              class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden overflow-auto py-4">
              <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl mx-4 flex flex-col max-h-[90vh]">
                <!-- En-tête de la popup -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                  <h3 class="font-semibold text-lg text-gray-800">Recadrer votre photo de profil</h3>
                  <button id="closeModal" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                      xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                      </path>
                    </svg>
                  </button>
                </div>

                <!-- Corps de la popup avec défilement -->
                <div class="p-6 overflow-auto flex-grow" style="max-height: calc(90vh - 160px);">
                  <div class="mb-6">
                    <img id="cropperImageModal" class="max-w-full mx-auto">
                  </div>
                  <p class="text-sm text-gray-500 mb-4 text-center">Faites glisser la zone de sélection pour recadrer
                    votre image.</p>
                </div>

                <!-- Pied de la popup (toujours visible) -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 sticky bottom-0">
                  <button id="cancelCropBtnModal"
                    class="bg-gray-100 text-gray-700 border border-gray-300 py-2 px-6 rounded-lg font-medium hover:bg-gray-200 transition">
                    Annuler
                  </button>
                  <button id="cropBtnModal" class="gradient-btn py-2 px-6">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                      xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Appliquer
                  </button>
                </div>
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

.xp-notification .icon {
  background-color: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 12px;
}

.xp-notification .content {
  flex: 1;
}

.xp-notification .points {
  font-size: 18px;
  font-weight: bold;
  margin-bottom: 2px;
}

.xp-notification .reason {
  font-size: 14px;
  opacity: 0.9;
}
          </style>
          <div class="w-full lg:w-1/2">
            <?php include 'include/update_availability.php'; ?>
          </div>
        </div>

        <!-- Bloc des disponibilités -->
      </div>

      <!-- Section Engagements (initialement cachée) -->
      <!-- Section Engagements (initialement cachée) -->
      <div id="engagementsTab" class="tab-content hidden">
  <div class="bg-white rounded-xl shadow-custom p-6 mb-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Mes associations</h2>

    <!-- Liste des associations -->
<!-- Liste des associations -->
<div id="associationsList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php
      // Récupérer les engagements de l'utilisateur via la table postulation
      $user_id = $_SESSION['user_id'];

      // Utiliser les colonnes qui existent réellement dans votre table
      $query = "SELECT a.*, p.postulation_id, p.postulation_date 
                FROM association a 
                JOIN postulation p ON a.association_id = p.postulation_association_id_fk 
                WHERE p.postulation_user_id_fk = ? 
                ORDER BY p.postulation_id DESC";

      try {
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
          while ($asso = $result->fetch_assoc()) {
            // Affichez vos données d'association
            ?>




            <div class="flex flex-col border border-gray-100 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
  <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-4 h-40 relative">
    <img src="<?php echo $asso['association_background_image'] ?>"
      alt="Logo <?php echo htmlspecialchars($asso['association_name']); ?>"
      class="w-full h-full object-cover rounded-lg">
      
    <!-- Badge de date d'abonnement -->
    <?php 
      $postulationDate = new DateTime($asso['postulation_date']);
      $formattedDate = $postulationDate->format('d M Y');
    ?>
    <div class="absolute bottom-3 left-3">
      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white bg-opacity-90 text-gray-800">
        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-purple-400" fill="currentColor" viewBox="0 0 8 8">
          <circle cx="4" cy="4" r="3" />
        </svg>
        Depuis le <?php echo $formattedDate; ?>
      </span>
    </div>
  </div>
  
  <div class="p-4">
    <div class="flex justify-between items-start mb-3">
      <h3 class="text-lg font-semibold text-gray-800">
        <?php echo htmlspecialchars($asso['association_name']); ?>
      </h3>
      
      <!-- Bouton pour se désabonner -->
      <button class="unfollow-btn text-sm text-gray-500 hover:text-red-500 transition-colors flex items-center" 
              data-association-id="<?php echo $asso['association_id']; ?>">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        Ne plus suivre
      </button>
    </div>
    
    <!-- Adresse de l'association -->
    <?php if(isset($asso['association_adress']) && !empty($asso['association_adress'])): 
      $address = json_decode($asso['association_adress'], true);
    ?>
    <div class="flex items-center text-sm text-gray-500 mb-3">
      <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
      </svg>
      <?php echo isset($address['name']) ? htmlspecialchars($address['name']) : 'Adresse non spécifiée'; ?>
    </div>
    <?php endif; ?>
    
    <!-- Description -->
    <div class="bg-gray-50 p-3 rounded-lg mb-3">
      <p class="text-gray-600 text-sm line-clamp-2"><?php echo htmlspecialchars($asso['association_desc']); ?></p>
    </div>
    
    <!-- Mission -->
    <div class="bg-gray-50 p-3 rounded-lg mb-4">
      <h4 class="text-sm font-medium text-gray-700 mb-1">Mission</h4>
      <p class="text-gray-600 text-sm line-clamp-2"><?php echo htmlspecialchars($asso['association_mission']); ?></p>
    </div>
    
    <!-- Boutons d'action -->
    <div class="flex justify-between items-center pt-2 mt-2 border-t border-gray-100">
      <div class="flex space-x-2">
        <?php if(!empty($asso['association_mail'])): ?>
        <a href="mailto:<?php echo htmlspecialchars($asso['association_mail']); ?>" 
           class="text-gray-500 hover:text-blue-600 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
          </svg>
        </a>
        <?php endif; ?>
      </div>
      
      <form id="viewForm_<?php echo $asso['association_id']; ?>" method="post" action="set_session.php" class="inline-block">
        <input type="hidden" name="association_id" value="<?php echo $asso['association_id']; ?>">
<a href="association.php?id=<?php echo $asso['association_id']; ?>" 
   class="gradient-btn py-1 px-4 text-sm">Voir détails</a>
      </form>
    </div>
  </div>
</div>
            <?php
          }
        } else {
          ?>
          <div class="text-center py-12 border border-dashed border-gray-300 rounded-lg bg-white">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
              </path>
            </svg>
            <h3 class="text-lg font-medium text-gray-600">Vous n'êtes pas encore engagé dans une association</h3>
            <p class="text-gray-500 mt-2 mb-6">Rejoignez une association pour commencer votre parcours bénévole</p>
            <a href="index.php" class="gradient-btn py-2 px-6">
              Découvrir les associations
            </a>
          </div>
          <?php
        }
        $stmt->close();
      } catch (Exception $e) {
        echo '<div class="p-4 bg-red-50 text-red-700 rounded-lg">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
      }
      ?>
    </div>
  </div>
<?php


?>
<div id="unfollowModal" class="fixed inset-0 bg-black/50 bg-opacity-50 flex items-center justify-center hidden z-50">
  <div class="bg-white p-6 rounded-lg shadow-lg w-96 max-w-full mx-4">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Confirmer le désabonnement</h3>
    <p class="text-gray-600 mb-6">Voulez-vous vraiment vous désabonner de cette association ? Vous ne recevrez plus de notifications concernant ses activités et missions.</p>
    <div class="flex justify-end space-x-3">
      <button id="cancelUnfollow" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
        Annuler
      </button>
      <button id="confirmUnfollow" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
        Confirmer
      </button>
    </div>
    <input type="hidden" id="unfollowAssociationId" value="">
  </div>
</div>
  <!-- Section des missions -->
<div class="bg-white rounded-xl shadow-custom p-6">
  <h2 class="text-2xl font-bold mb-6 text-gray-800">Mes missions</h2>

  <?php
  // Vérifier d'abord si la table applications existe
  $tableCheck = $conn->query("SHOW TABLES LIKE 'applications'");
  $applicationsTableExists = $tableCheck->num_rows > 0;

  if ($applicationsTableExists) {
    try {
      // Récupérer les missions auxquelles l'utilisateur est inscrit
      // Correction de la requête SQL pour utiliser la structure correcte des tables
      $missionsQuery = "
        SELECT m.*, a.association_name, a.association_profile_picture, 
               app.status, app.application_date, app.motivation
        FROM applications app
        JOIN missions m ON app.mission_id = m.mission_id
        JOIN association a ON m.association_id = a.association_id
        WHERE app.volunteer_id = ?
        ORDER BY app.application_date DESC
      ";

      $stmt = $conn->prepare($missionsQuery);
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $missionsResult = $stmt->get_result();

      if ($missionsResult && $missionsResult->num_rows > 0) {
        ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php while ($mission = $missionsResult->fetch_assoc()) {
            // Formater la date
            $applicationDate = new DateTime($mission['application_date']);
            $formattedDate = $applicationDate->format('d M Y');
            
            // Définir le statut visuel
            $statusClass = 'bg-yellow-100 text-yellow-800';
            $statusLabel = 'En attente';
            
            if ($mission['status'] === 'approved') {
              $statusClass = 'bg-green-100 text-green-800';
              $statusLabel = 'Approuvé';
            } elseif ($mission['status'] === 'rejected') {
              $statusClass = 'bg-red-100 text-red-800';
              $statusLabel = 'Refusé';
            } elseif ($mission['status'] === 'completed') {
              $statusClass = 'bg-blue-100 text-blue-800';
              $statusLabel = 'Terminé';
            }
            
            // Vérifier si la mission est passée
            $missionDate = isset($mission['mission_date']) ? new DateTime($mission['mission_date']) : new DateTime();
            $now = new DateTime();
            $isPast = $missionDate < $now;
            ?>
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all duration-300">
              <?php if (!empty($mission['image_url'])): ?>
              <div class="h-40 overflow-hidden relative">
                <img src="<?php echo htmlspecialchars($mission['image_url']); ?>" 
                     alt="<?php echo htmlspecialchars($mission['title']); ?>" 
                     class="w-full h-full object-cover">
                <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-transparent to-black opacity-60"></div>
                <div class="absolute bottom-3 left-3">
                  <span class="px-2 py-1 text-xs rounded-full bg-white bg-opacity-90 text-gray-900 font-semibold">
                    <?php echo $formattedDate; ?>
                  </span>
                </div>
                <div class="absolute top-3 right-3">
                  <span class="px-2 py-1 text-xs rounded-full <?php echo $statusClass; ?>">
                    <?php echo $statusLabel; ?>
                  </span>
                </div>
              </div>
              <?php else: ?>
              <div class="h-40 bg-gradient-to-r from-purple-100 to-pink-100 relative">
                <div class="absolute top-3 right-3">
                  <span class="px-2 py-1 text-xs rounded-full <?php echo $statusClass; ?>">
                    <?php echo $statusLabel; ?>
                  </span>
                </div>
                <div class="absolute bottom-3 left-3">
                  <span class="px-2 py-1 text-xs rounded-full bg-white bg-opacity-90 text-gray-900 font-semibold">
                    <?php echo $formattedDate; ?>
                  </span>
                </div>
                <div class="flex items-center justify-center h-full">
                  <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                </div>
              </div>
              <?php endif; ?>
              
              <div class="p-4">
                <div class="flex items-center mb-3">
                  <img src="<?php echo !empty($mission['association_profile_picture']) ? 
                                  htmlspecialchars($mission['association_profile_picture']) : 
                                  'assets/img/default-asso.png'; ?>" 
                       alt="Logo" class="w-6 h-6 rounded-full mr-2">
                  <span class="text-xs text-gray-600">
                    <?php echo htmlspecialchars($mission['association_name']); ?>
                  </span>
                </div>
                
                <h3 class="text-lg font-bold text-gray-800 mb-2">
                  <?php echo htmlspecialchars($mission['title']); ?>
                </h3>
                
                <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                  <?php echo htmlspecialchars($mission['description'] ?? 'Aucune description disponible'); ?>
                </p>
                
                <?php if (!empty($mission['motivation'])): ?>
                <div class="bg-gray-50 p-2 rounded-lg mb-3">
                  <p class="text-xs text-gray-500 italic line-clamp-2">
                    "<?php echo htmlspecialchars($mission['motivation']); ?>"
                  </p>
                </div>
                <?php endif; ?>
                
                <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100">
                  <?php if ($isPast): ?>
                    <?php if ($mission['status'] == 'completed'): ?>
                      <span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full">
                        Mission terminée
                      </span>
                    <?php else: ?>
                      <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">
                        Date passée
                      </span>
                    <?php endif; ?>
                  <?php elseif (isset($mission['mission_date'])): ?>
                    <?php 
                    $daysRemaining = $now->diff($missionDate)->days;
                    $timeLabel = $daysRemaining === 0 ? "Aujourd'hui" : "Dans $daysRemaining jours";
                    ?>
                    <span class="text-gray-600 text-xs">
                      <?php echo $timeLabel; ?>
                    </span>
                  <?php else: ?>
                    <span class="text-gray-600 text-xs">Date non spécifiée</span>
                  <?php endif; ?>
                  
                  <a href="mission.php?id=<?php echo $mission['mission_id']; ?>" 
                     class="text-primary hover:text-primary-dark text-sm font-medium transition-colors">
                    Détails
                  </a>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      <?php
      } else {
        ?>
        <div class="text-center py-12 border border-dashed border-gray-300 rounded-lg bg-white">
          <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <h3 class="text-lg font-medium text-gray-600">Vous n'êtes inscrit à aucune mission</h3>
          <p class="text-gray-500 mt-2 mb-6">Rejoignez une mission pour commencer votre engagement bénévole</p>
          <a href="missions.php" class="gradient-btn py-2 px-6">
            Découvrir les missions
          </a>
        </div>
        <?php
      }
      $stmt->close();
    } catch (Exception $e) {
      echo '<div class="p-4 bg-red-50 text-red-700 rounded-lg">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
  } else {
    // Si la table applications n'existe pas, afficher un message approprié
    ?>
    <div class="text-center py-12 border border-dashed border-gray-300 rounded-lg bg-white">
      <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
      </svg>
      <h3 class="text-lg font-medium text-gray-600">Système de missions en développement</h3>
      <p class="text-gray-500 mt-2 mb-6">Le système de candidature aux missions est en cours de développement. Revenez bientôt pour découvrir les nouvelles opportunités !</p>
    </div>
    <?php
  }
  ?>
</div>
</div>

<style>
  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
</style>

        <!-- Autres sections pour les autres onglets (statistiques, certifications, etc.) -->
                  <!-- Contenu pour les statistiques -->
                  <div id="statsTab" class="tab-content hidden">
  <div class="bg-white rounded-xl shadow-custom p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Mes statistiques</h2>
    
    <div class="text-center py-12">
      <div class="relative mx-auto w-64 h-64 mb-8">
        <!-- Animation SVG -->
        <svg class="animate-spin-slow absolute inset-0" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
          <circle cx="50" cy="50" r="45" fill="none" stroke="#ffd6e2" stroke-width="8" />
          <path d="M50 5 A 45 45 0 0 1 95 50" fill="none" stroke="#CF3275" stroke-width="8" stroke-linecap="round" />
        </svg>
        
        <!-- Icône centrale -->
        <div class="absolute inset-0 flex items-center justify-center">
          <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
        </div>
      </div>
      
      <h3 class="text-xl font-semibold text-gray-700 mb-3">Statistiques en cours de développement</h3>
      <p class="text-gray-500 max-w-md mx-auto mb-8">Nous travaillons activement sur cette fonctionnalité pour vous offrir une vue détaillée de votre parcours bénévole et de votre impact. Revenez bientôt !</p>
      
      <!-- Fausse barre de progression -->
      <div class="max-w-md mx-auto mb-6">
        <div class="bg-gray-100 rounded-full h-4 overflow-hidden">
          <div class="bg-gradient-to-r from-primary to-secondary h-full rounded-full" style="width: 65%"></div>
        </div>
        <p class="text-sm text-gray-500 mt-2">Progression: 65%</p>
      </div>
      
      <!-- Aperçu de ce qui arrive -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-2xl mx-auto mt-10">
        <div class="bg-purple-50 rounded-lg p-4 border border-purple-100 flex flex-col items-center">
          <div class="rounded-full bg-purple-100 p-3 mb-3">
            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h4 class="font-medium text-gray-700">Heures de bénévolat</h4>
        </div>
        
        <div class="bg-pink-50 rounded-lg p-4 border border-pink-100 flex flex-col items-center">
          <div class="rounded-full bg-pink-100 p-3 mb-3">
            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h4 class="font-medium text-gray-700">Missions accomplies</h4>
        </div>
        
        <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100 flex flex-col items-center">
          <div class="rounded-full bg-indigo-100 p-3 mb-3">
            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
          </div>
          <h4 class="font-medium text-gray-700">Impact social</h4>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  @keyframes spin-slow {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  .animate-spin-slow {
    animation: spin-slow 10s linear infinite;
  }
</style>

<div id="certificationsTab" class="tab-content hidden">
  <!-- Contenu pour les certifications -->
  <?php
  // Récupérer les données d'XP de l'utilisateur
  $stmt = $conn->prepare("SELECT * FROM user_experience WHERE user_id = ?");
  $stmt->bind_param("i", $_SESSION['user_id']);
  $stmt->execute();
  $result = $stmt->get_result();

  // Valeurs par défaut
  $xp_data = [
      'total_points' => 0,
      'current_level' => 1,
      'points_to_next_level' => 100
  ];

  if ($result->num_rows > 0) {
      $xp_data = $result->fetch_assoc();
  }

  // Noms des niveaux
  $level_names = [
      1 => "Bénévole Débutant",
      2 => "Bénévole Actif",
      3 => "Bénévole Engagé",
      4 => "Bénévole Expérimenté",
      5 => "Bénévole Expert",
      6 => "Bénévole Maître",
      7 => "Bénévole Émérite",
      8 => "Bénévole Légendaire",
      9 => "Bénévole Héroïque",
      10 => "Bénévole Mythique"
  ];

  // Seuils de niveaux
  $levels = [
      1 => 0,
      2 => 100,
      3 => 250,
      4 => 500,
      5 => 1000,
      6 => 2000,
      7 => 3500,
      8 => 5000,
      9 => 7500,
      10 => 10000
  ];

  $current_level = $xp_data['current_level'];
  $prev_level_xp = $levels[$current_level]; // XP minimale du niveau actuel
  $next_level_xp = $xp_data['points_to_next_level']; // XP minimale du niveau suivant

  // Calculer le pourcentage de progression entre niveau actuel et suivant
  $current_level_progress = (($xp_data['total_points'] - $prev_level_xp) / ($next_level_xp - $prev_level_xp)) * 100;
  $current_level_progress = min(100, max(0, $current_level_progress)); // Limiter entre 0 et 100%

  $level_name = isset($level_names[$xp_data['current_level']]) ? $level_names[$xp_data['current_level']] : "Niveau " . $xp_data['current_level'];
  $next_level_name = isset($level_names[$xp_data['current_level'] + 1]) ? $level_names[$xp_data['current_level'] + 1] : "Niveau " . ($xp_data['current_level'] + 1);
  ?>

  <div class="bg-white rounded-xl shadow-custom p-6 mb-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Niveau et progression</h2>
    
    <!-- Barre de progression (code existant) -->
    <div class="bg-gradient-to-r from-purple-100 to-pink-100 rounded-lg p-5 mb-8">
      <div class="flex justify-between mb-3">
        <h3 class="text-xl font-bold text-purple-900">Niveau <?= $xp_data['current_level'] ?>: <?= $level_name ?></h3>
        <span class="bg-purple-200 text-purple-800 px-3 py-1 rounded-full font-bold"><?= $xp_data['total_points'] ?> XP</span>
      </div>
      <div class="w-full bg-white rounded-full h-4 overflow-hidden">
        <div class="bg-gradient-to-r from-primary to-secondary h-full rounded-full" style="width: <?= $current_level_progress ?>%"></div>
      </div>
      <div class="flex justify-between mt-1 text-sm text-gray-700">
        <span><?= $xp_data['total_points'] ?> / <?= $next_level_xp ?> XP pour le niveau <?= $xp_data['current_level'] + 1 ?></span>
        <span>Prochain niveau: <?= $next_level_name ?></span>
      </div>
    </div>
    
    <!-- Ajouter cette section pour les badges -->
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Mes badges</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
      <?php
      // Badges correspondant aux niveaux
      $badges = [
        1 => [
          'name' => 'Bénévole Débutant',
          'icon' => 'assets/icons/badges/level1.svg',
          'description' => 'Premiers pas dans le monde du bénévolat'
        ],
        2 => [
          'name' => 'Bénévole Actif',
          'icon' => 'assets/icons/badges/level2.svg',
          'description' => 'Vous êtes devenu un bénévole actif'
        ],
        3 => [
          'name' => 'Bénévole Engagé',
          'icon' => 'assets/icons/badges/level3.svg',
          'description' => 'Vous démontrez un engagement sérieux'
        ],
        4 => [
          'name' => 'Bénévole Expérimenté',
          'icon' => 'assets/icons/badges/level4.svg',
          'description' => 'Votre expérience commence à s\'accumuler'
        ],
        5 => [
          'name' => 'Bénévole Expert',
          'icon' => 'assets/icons/badges/level5.svg',
          'description' => 'Vous êtes maintenant un expert du bénévolat'
        ],
        6 => [
          'name' => 'Bénévole Maître',
          'icon' => 'assets/icons/badges/level6.svg',
          'description' => 'Vous maîtrisez l\'art du bénévolat'
        ],
        7 => [
          'name' => 'Bénévole Émérite',
          'icon' => 'assets/icons/badges/level7.svg',
          'description' => 'Votre contribution est exemplaire'
        ],
        8 => [
          'name' => 'Bénévole Légendaire',
          'icon' => 'assets/icons/badges/level8.svg',
          'description' => 'Votre impact est légendaire'
        ],
        9 => [
          'name' => 'Bénévole Héroïque',
          'icon' => 'assets/icons/badges/level9.svg',
          'description' => 'Vous êtes un héros du bénévolat'
        ],
        10 => [
          'name' => 'Bénévole Mythique',
          'icon' => 'assets/icons/badges/level10.svg',
          'description' => 'Vous avez atteint le statut mythique'
        ],
      ];

      // Afficher les badges débloqués et les badges à venir
      for ($i = 1; $i <= 10; $i++) {
        $unlocked = $i <= $xp_data['current_level'];
        // Utiliser une icône par défaut si le fichier n'existe pas encore
        $icon = file_exists($_SERVER['DOCUMENT_ROOT'] . '/HeartHive/HeartHive/frontend/' . $badges[$i]['icon']) 
                ? $badges[$i]['icon'] 
                : 'https://api.dicebear.com/7.x/shapes/svg?seed=level' . $i;
        ?>
        <div class="badge-card bg-white rounded-lg overflow-hidden border <?= $unlocked ? 'border-purple-300' : 'border-gray-200' ?> transform transition-all duration-300 hover:shadow-lg <?= $unlocked ? 'hover:-translate-y-1' : 'opacity-50' ?>">
          <div class="p-4 text-center">
            <div class="relative w-24 h-24 mx-auto mb-3">
              <img src="<?= $icon ?>" alt="Badge niveau <?= $i ?>" class="w-full h-full object-contain">
              <?php if (!$unlocked) { ?>
                <div class="absolute inset-0 bg-gray-200 bg-opacity-60 flex items-center justify-center rounded-full">
                  <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                  </svg>
                </div>
              <?php } ?>
            </div>
            <h3 class="font-bold text-sm text-gray-800"><?= $badges[$i]['name'] ?></h3>
            <p class="text-xs text-gray-500 mt-1"><?= $badges[$i]['description'] ?></p>
            <?php if ($unlocked) { ?>
              <span class="inline-block mt-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Débloqué</span>
            <?php } else { ?>
              <span class="inline-block mt-2 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">Niveau <?= $i ?> requis</span>
            <?php } ?>
          </div>
        </div>
        <?php
      }
      ?>
    </div>
  </div>
  
  <!-- Autres réalisations -->
  <div class="bg-white rounded-xl shadow-custom p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Autres réalisations</h2>
    <p class="text-gray-500">D'autres badges peuvent être débloqués en accomplissant diverses actions sur la plateforme.</p>
    
    <!-- Exemples d'autres types de badges -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-6">
      <?php
      // Vérifier si le profil est complet
      $user_id = $_SESSION['user_id'];
      $hasBio = !empty($_SESSION['user_bio']) && strlen($_SESSION['user_bio']) > 10;
      $hasLocation = isset($_SESSION['user_adress']) && $_SESSION['user_adress'] !== '';
      $hasProfilePic = $_SESSION['user_profile_picture'] && !strpos($_SESSION['user_profile_picture'], 'default.webp');
      $profileComplete = $hasBio && $hasLocation && $hasProfilePic;
      
      // Vérifier s'il a postulé à une association
      $check_query = "SELECT COUNT(*) as count FROM postulation WHERE postulation_user_id_fk = ?";
      $check_stmt = $conn->prepare($check_query);
      $check_stmt->bind_param("i", $user_id);
      $check_stmt->execute();
      $result = $check_stmt->get_result();
      $row = $result->fetch_assoc();
      $hasApplied = $row['count'] > 0;
      
      // Exemples de badges spéciaux (à ajuster selon votre logique métier)
      $special_badges = [
        [
          'name' => 'Premier engagement',
          'icon' => 'https://api.dicebear.com/7.x/shapes/svg?seed=first_mission',
          'description' => 'Vous avez rejoint votre première association',
          'unlocked' => $hasApplied
        ],
        [
          'name' => 'Profil complet',
          'icon' => 'https://api.dicebear.com/7.x/shapes/svg?seed=complete_profile',
          'description' => 'Vous avez complété toutes les informations de votre profil',
          'unlocked' => $profileComplete
        ],
        [
          'name' => 'Connexion régulière',
          'icon' => 'https://api.dicebear.com/7.x/shapes/svg?seed=login_streak',
          'description' => '7 jours de connexion consécutifs',
          'unlocked' => false // À implémenter
        ],
        [
          'name' => 'Première mission',
          'icon' => 'https://api.dicebear.com/7.x/shapes/svg?seed=mission_complete',
          'description' => 'Vous avez complété votre première mission',
          'unlocked' => false // À implémenter
        ]
      ];

      foreach ($special_badges as $badge) {
        ?>
        <div class="badge-card bg-white rounded-lg overflow-hidden border <?= $badge['unlocked'] ? 'border-purple-300' : 'border-gray-200' ?> transform transition-all duration-300 hover:shadow-lg <?= $badge['unlocked'] ? 'hover:-translate-y-1' : 'opacity-50' ?>">
          <div class="p-4 text-center">
            <div class="relative w-24 h-24 mx-auto mb-3">
              <img src="<?= $badge['icon'] ?>" alt="<?= $badge['name'] ?>" class="w-full h-full object-contain">
              <?php if (!$badge['unlocked']) { ?>
                <div class="absolute inset-0 bg-gray-200 bg-opacity-60 flex items-center justify-center rounded-full">
                  <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                  </svg>
                </div>
              <?php } ?>
            </div>
            <h3 class="font-bold text-sm text-gray-800"><?= $badge['name'] ?></h3>
            <p class="text-xs text-gray-500 mt-1"><?= $badge['description'] ?></p>
            <?php if ($badge['unlocked']) { ?>
              <span class="inline-block mt-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Débloqué</span>
            <?php } else { ?>
              <span class="inline-block mt-2 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">Verrouillé</span>
            <?php } ?>
          </div>
        </div>
        <?php
      }
      ?>
    </div>
  </div>
</div>

<style>
  @keyframes badge-glow {
    0%, 100% { box-shadow: 0 0 10px rgba(139, 92, 246, 0.5); }
    50% { box-shadow: 0 0 20px rgba(139, 92, 246, 0.8); }
  }

  .badge-card.border-purple-300 {
    transition: all 0.3s ease;
  }

  .badge-card.border-purple-300:hover {
    animation: badge-glow 2s infinite;
  }

  /* Animation pour les nouveaux badges */
  @keyframes badge-unlock {
    0% { transform: scale(0.8); opacity: 0; }
    70% { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
  }

  .badge-unlock-animation {
    animation: badge-unlock 1s forwards;
  }
</style>

<div id="messagesTab" class="tab-content hidden">
  <div class="bg-white rounded-xl shadow-custom p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Messagerie</h2>
    
    <div class="text-center py-12">
      <div class="relative mx-auto w-64 h-64 mb-8">
        <!-- Animation d'enveloppe pulsante -->
        <div class="absolute inset-0 bg-purple-100 rounded-full animate-pulse-slow opacity-50"></div>
        
        <!-- Icône d'enveloppe -->
        <div class="absolute inset-0 flex items-center justify-center">
          <svg class="w-24 h-24 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
          </svg>
        </div>
      </div>
      
      <h3 class="text-xl font-semibold text-gray-700 mb-3">Système de messagerie en développement</h3>
      <p class="text-gray-500 max-w-md mx-auto mb-8">Notre équipe travaille actuellement sur un système de messagerie performant pour vous permettre de communiquer directement avec les associations et les autres bénévoles. Cette fonctionnalité sera bientôt disponible !</p>
      
      <!-- Fausse barre de progression -->
      <div class="max-w-md mx-auto mb-6">
        <div class="bg-gray-100 rounded-full h-4 overflow-hidden">
          <div class="bg-gradient-to-r from-purple-300 to-pink-300 h-full rounded-full" style="width: 75%"></div>
        </div>
        <p class="text-sm text-gray-500 mt-2">Progression: 75%</p>
      </div>
      
      <!-- Fonctionnalités à venir -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-2xl mx-auto mt-10">
        <div class="bg-purple-50 rounded-lg p-4 border border-purple-100 flex flex-col items-center">
          <div class="rounded-full bg-purple-100 p-3 mb-3">
            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
            </svg>
          </div>
          <h4 class="font-medium text-gray-700">Messages privés</h4>
        </div>
        
        <div class="bg-pink-50 rounded-lg p-4 border border-pink-100 flex flex-col items-center">
          <div class="rounded-full bg-pink-100 p-3 mb-3">
            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
          <h4 class="font-medium text-gray-700">Discussions de groupe</h4>
        </div>
        
        <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100 flex flex-col items-center">
          <div class="rounded-full bg-indigo-100 p-3 mb-3">
            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
          </div>
          <h4 class="font-medium text-gray-700">Notifications</h4>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="settingsTab" class="tab-content hidden">
  <div class="bg-white rounded-xl shadow-custom p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Paramètres</h2>
    
    <div class="text-center py-12">
      <div class="relative mx-auto w-64 h-64 mb-8">
        <!-- Animation d'engrenage rotatif -->
        <svg class="animate-spin-slow absolute inset-0" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
          <circle cx="50" cy="50" r="45" fill="none" stroke="#e2e8f0" stroke-width="8" />
          <path d="M50 5 A 45 45 0 0 1 95 50" fill="none" stroke="#ffd6e2" stroke-width="8" stroke-linecap="round" />
        </svg>
        
        <!-- Icône d'engrenage -->
        <div class="absolute inset-0 flex items-center justify-center">
          <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
        </div>
      </div>
      
      <h3 class="text-xl font-semibold text-gray-700 mb-3">Paramètres en cours de développement</h3>
      <p class="text-gray-500 max-w-md mx-auto mb-8">Nous travaillons actuellement sur une interface de paramètres complète pour vous permettre de personnaliser votre expérience sur HeartHive. Revenez bientôt pour découvrir ces nouvelles fonctionnalités !</p>
      
      <!-- Fausse barre de progression -->
      <div class="max-w-md mx-auto mb-6">
        <div class="bg-gray-100 rounded-full h-4 overflow-hidden">
          <div class="bg-gradient-to-r from-pink-300 to-red-300 h-full rounded-full" style="width: 60%"></div>
        </div>
        <p class="text-sm text-gray-500 mt-2">Progression: 60%</p>
      </div>
      
      <!-- Paramètres à venir -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-2xl mx-auto mt-10">
        <div class="bg-pink-50 rounded-lg p-4 border border-pink-100 flex flex-col items-center">
          <div class="rounded-full bg-pink-100 p-3 mb-3">
            <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
          </div>
          <h4 class="font-medium text-gray-700">Confidentialité</h4>
        </div>
        
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-100 flex flex-col items-center">
          <div class="rounded-full bg-blue-100 p-3 mb-3">
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
          </div>
          <h4 class="font-medium text-gray-700">Notifications</h4>
        </div>
        
        <div class="bg-purple-50 rounded-lg p-4 border border-purple-100 flex flex-col items-center">
          <div class="rounded-full bg-purple-100 p-3 mb-3">
            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
            </svg>
          </div>
          <h4 class="font-medium text-gray-700">Langue</h4>
        </div>
      </div>
    </div>
  </div>
</div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
  // Attribuer des points pour la connexion quotidienne
  fetch('../backend/xp.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'action=daily_login&details=login'
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      showXPNotification(data.points, "Connexion quotidienne!");
    // Vérifier si l'utilisateur a monté de niveau
    if (data.level_up) {
      // Noms des niveaux (à synchroniser avec le backend)
      const level_names = {
        1: "Bénévole Débutant",
        2: "Bénévole Actif",
        3: "Bénévole Engagé",
        4: "Bénévole Expérimenté",
        5: "Bénévole Expert",
        6: "Bénévole Maître",
        7: "Bénévole Émérite",
        8: "Bénévole Légendaire",
        9: "Bénévole Héroïque",
        10: "Bénévole Mythique"
      };
      
      // Afficher la notification de montée de niveau
      const levelName = level_names[data.new_level] || "Niveau " + data.new_level;
      setTimeout(() => {
        showLevelUpNotification(data.new_level, levelName);
      }, 1500); // Montrer après la notification XP
    }
  }
})
  .catch(error => console.error('Erreur XP:', error));
  
  // Le reste de votre code...
});
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Éléments DOM pour l'upload et le recadrage
      const profilePicInput = document.getElementById('profilePicInput');
      const selectImageBtn = document.getElementById('selectImageBtn');
      const previewImage = document.getElementById('previewImage');
      const croppedImageData = document.getElementById('croppedImageData');

      // Éléments DOM pour la popup modale
      const cropperModal = document.getElementById('cropperModal');
      const cropperImageModal = document.getElementById('cropperImageModal');
      const cropBtnModal = document.getElementById('cropBtnModal');
      const cancelCropBtnModal = document.getElementById('cancelCropBtnModal');
      const closeModal = document.getElementById('closeModal');

      // Variable pour stocker l'instance du cropper
      let cropper;

      // Ouvrir le sélecteur de fichier quand on clique sur le bouton
      if (selectImageBtn) {
        selectImageBtn.addEventListener('click', function () {
          profilePicInput.click();
        });
      }

      // Initialiser le cropper quand une image est sélectionnée
      if (profilePicInput) {
        profilePicInput.addEventListener('change', function (e) {
          const files = e.target.files;

          if (!files || !files.length) return;

          const file = files[0];

          // Vérifier que c'est bien une image
          if (!file.type.match('image.*')) {
            alert('Veuillez sélectionner une image');
            return;
          }

          // Créer un blob URL pour l'image
          const imageURL = URL.createObjectURL(file);

          // Ouvrir la popup de recadrage
          openCropperModal(imageURL);
        });
      }

      // Fonction pour ouvrir la popup de recadrage
      function openCropperModal(imageURL) {
        // Initialiser l'image dans la popup
        cropperImageModal.src = imageURL;

        // Afficher la popup
        cropperModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Empêcher le défilement de la page

        // Détruire l'instance précédente si elle existe
        if (cropper) {
          cropper.destroy();
        }

        // Créer une nouvelle instance de cropper après un court délai
        // pour s'assurer que l'image est chargée
        setTimeout(() => {
          cropper = new Cropper(cropperImageModal, {
            aspectRatio: 1, // Rapport carré pour photo de profil
            viewMode: 1,    // Restreindre la zone de recadrage à l'image
            guides: true,   // Afficher les guides
            center: true,   // Centrer l'image
            highlight: true,// Mettre en évidence la zone de recadrage
            background: false,
            autoCropArea: 0.8, // Taille par défaut de la zone de recadrage (80% de l'image)
            responsive: true,
            ready: function () {
              // Le cropper est prêt
            }
          });
        }, 100);
      }

      // Fonction pour fermer la popup
      function closeCropperModal() {
        cropperModal.classList.add('hidden');
        document.body.style.overflow = ''; // Rétablir le défilement de la page

        if (cropper) {
          cropper.destroy();
          cropper = null;
        }
      }

      // Fermer la popup quand on clique sur le bouton Fermer
      if (closeModal) {
        closeModal.addEventListener('click', closeCropperModal);
      }

      // Fermer la popup quand on clique sur Annuler
      if (cancelCropBtnModal) {
        cancelCropBtnModal.addEventListener('click', function () {
          closeCropperModal();
          // Réinitialiser l'input de fichier
          profilePicInput.value = '';
        });
      }

      // Appliquer le recadrage quand on clique sur Appliquer
      if (cropBtnModal) {
        cropBtnModal.addEventListener('click', function () {
          if (!cropper) return;

          // Récupérer l'image recadrée sous forme de canvas
          const canvas = cropper.getCroppedCanvas({
            width: 300,    // Largeur maximale
            height: 300,   // Hauteur maximale
            fillColor: '#fff',
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
          });

          if (!canvas) return;

          // Convertir le canvas en URL pour l'aperçu
          const croppedURL = canvas.toDataURL('image/jpeg', 0.9);

          // Mettre à jour l'aperçu et stocker l'image recadrée
          previewImage.src = croppedURL;
          croppedImageData.value = croppedURL;

          // Fermer la popup
          closeCropperModal();
        });
      }

      // Fermer la popup si on clique en dehors
      cropperModal.addEventListener('click', function (e) {
        if (e.target === cropperModal) {
          closeCropperModal();
        }
      });

      // Fermer la popup avec la touche Echap
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !cropperModal.classList.contains('hidden')) {
          closeCropperModal();
        }
      });

      // ... Reste de votre code JavaScript existant ...
    });






    // Ajoutez ce code à la fin de votre script existant
    document.addEventListener('DOMContentLoaded', function () {
      // Gestionnaire du champ de localisation
      const locationInput = document.getElementById('location');
      const locationResults = document.getElementById('locationResults');
      const locationCoordinates = document.getElementById('locationCoordinates');
      const locationRange = document.getElementById('locationRange');
      const rangeValue = document.getElementById('rangeValue');

      // Afficher la valeur du rayon
      if (locationRange && rangeValue) {
        locationRange.addEventListener('input', function () {
          rangeValue.textContent = this.value + ' km';
        });

        // Initialiser avec la valeur actuelle si disponible
        if (typeof userData !== 'undefined' && userData.user_adress) {
          try {
            const addressData = JSON.parse(userData.user_adress);
            if (addressData.range) {
              locationRange.value = addressData.range;
              rangeValue.textContent = addressData.range + ' km';
            }
          } catch (e) {
            console.error('Erreur lors du parsing des données d\'adresse:', e);
          }
        }
      }

      // Recherche de ville avec OpenStreetMap Nominatim
      if (locationInput && locationResults) {
        let searchTimeout;

        locationInput.addEventListener('input', function () {
          clearTimeout(searchTimeout);
          const query = this.value.trim();

          if (query.length < 3) {
            locationResults.classList.add('hidden');
            return;
          }

          searchTimeout = setTimeout(() => {
            fetchCities(query);
          }, 500);
        });

        // Cacher les résultats quand on clique ailleurs
        document.addEventListener('click', function (e) {
          if (!locationInput.contains(e.target) && !locationResults.contains(e.target)) {
            locationResults.classList.add('hidden');
          }
        });

        // Fonction pour récupérer les villes
        async function fetchCities(query) {
          try {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=5&q=${encodeURIComponent(query)}&addressdetails=1`);
            const data = await response.json();

            displayCityResults(data);
          } catch (error) {
            console.error('Erreur lors de la recherche de villes:', error);
          }
        }

        // Afficher les résultats
        function displayCityResults(cities) {
          locationResults.innerHTML = '';

          if (cities.length === 0) {
            locationResults.innerHTML = '<div class="p-3 text-gray-500">Aucun résultat trouvé</div>';
            locationResults.classList.remove('hidden');
            return;
          }

          cities.forEach(city => {
            const cityName = city.display_name.split(',')[0];
            const country = city.address.country || '';

            const div = document.createElement('div');
            div.className = 'p-3 hover:bg-gray-100 cursor-pointer';
            div.innerHTML = `
          <div class="font-medium">${cityName}</div>
          <div class="text-sm text-gray-500">${city.display_name}</div>
        `;

            div.addEventListener('click', function () {
              locationInput.value = cityName;
              locationCoordinates.value = JSON.stringify([parseFloat(city.lon), parseFloat(city.lat)]);
              locationResults.classList.add('hidden');
            });

            locationResults.appendChild(div);
          });

          locationResults.classList.remove('hidden');
        }
      }

      // Traitement du nom et prénom
      const nameInput = document.getElementById('name');

      if (nameInput) {
        nameInput.addEventListener('blur', function () {
          const fullName = this.value.trim();
          const nameParts = fullName.split(' ');

          // Stocker dans des variables ou champs cachés pour utilisation lors de la soumission
          if (nameParts.length >= 2) {
            const firstName = nameParts[0];
            const lastName = nameParts.slice(1).join(' ');

            // Stocker dans des champs cachés
            if (!document.getElementById('firstName')) {
              const firstNameInput = document.createElement('input');
              firstNameInput.type = 'hidden';
              firstNameInput.id = 'firstName';
              firstNameInput.name = 'firstName';
              nameInput.parentNode.appendChild(firstNameInput);
            }

            if (!document.getElementById('lastName')) {
              const lastNameInput = document.createElement('input');
              lastNameInput.type = 'hidden';
              lastNameInput.id = 'lastName';
              lastNameInput.name = 'lastName';
              nameInput.parentNode.appendChild(lastNameInput);
            }

            document.getElementById('firstName').value = firstName;
            document.getElementById('lastName').value = lastName;
          }
        });
      }
      // Gérer la soumission du formulaire
      const profileForm = document.getElementById('profileForm');

      if (profileForm) {
        profileForm.addEventListener('submit', function (e) {
          e.preventDefault();

          // Créer l'objet FormData
          const formData = new FormData(this);

          // Vérifier si on a une image recadrée
          const croppedImageData = document.getElementById('croppedImageData');
          if (croppedImageData && croppedImageData.value) {
            // L'image recadrée est déjà dans le formData via le champ caché
            console.log('Envoi de l\'image recadrée');

            // Envoyer l'image recadrée au serveur
            fetch('../backend/update_profile_picture.php', {
              method: 'POST',
              body: formData
            })
              .then(response => response.json())
              .then(data => {
  if (data.status === 'success') {
    console.log('Photo de profil mise à jour avec succès');
    // Mettre à jour l'image de profil sur la page
    document.getElementById('profilePic').src = data.image_path;
    
    // Ajouter des points XP pour la photo de profil
    fetch('../backend/xp.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'action=add_profile_picture&details=photo'
    })
    .then(response => response.json())
    .then(xpData => {
      if (xpData.status === 'success') {
        showXPNotification(xpData.points, "Photo ajoutée!");
      // Vérifier si l'utilisateur a monté de niveau
    if (data.level_up) {
      // Noms des niveaux (à synchroniser avec le backend)
      const level_names = {
        1: "Bénévole Débutant",
        2: "Bénévole Actif",
        3: "Bénévole Engagé",
        4: "Bénévole Expérimenté",
        5: "Bénévole Expert",
        6: "Bénévole Maître",
        7: "Bénévole Émérite",
        8: "Bénévole Légendaire",
        9: "Bénévole Héroïque",
        10: "Bénévole Mythique"
      };
      
      // Afficher la notification de montée de niveau
      const levelName = level_names[data.new_level] || "Niveau " + data.new_level;
      setTimeout(() => {
        showLevelUpNotification(data.new_level, levelName);
      }, 1500); // Montrer après la notification XP
    }
  }
})
    .catch(error => console.error('Erreur XP:', error));
  } else {
    console.error('Erreur lors de la mise à jour de la photo de profil:', data.message);
  }
})
              .catch(error => {
                console.error('Erreur:', error);
              });
          }

          // Ajouter les informations de localisation au format JSON
          if (locationInput.value && locationCoordinates.value) {
            const locationData = {
              name: locationInput.value,
              coordinates: JSON.parse(locationCoordinates.value),
              range: parseInt(locationRange.value)
            };

            formData.append('location_data', JSON.stringify(locationData));
          }

          // Envoyer les données au serveur
          fetch('../backend/update_profile.php', {
            method: 'POST',
            body: formData
          })
            .then(response => response.json())
            .then(data => {
  if (data.status === 'success') {
    // Vérifier si le profil est complet pour donner de l'XP
    checkProfileCompleteness(formData);
    
    alert('Profil mis à jour avec succès');
    // Recharger la page
    window.location.reload();
  } else {
    alert('Erreur: ' + data.message);
  }
})
            .catch(error => {
              console.error('Erreur:', error);
              alert('Une erreur est survenue lors de la mise à jour du profil');
            });
        });
      }
    });
  </script>


  <script>
    function submitAndRedirect(associationId) {
      console.log("Fonction appelée avec ID:", associationId);

      // Vérifier si le formulaire existe
      const form = document.getElementById('viewForm_' + associationId);
      if (!form) {
        console.error("Formulaire non trouvé avec ID: viewForm_" + associationId);
        return;
      }

      console.log("Formulaire trouvé, création du FormData");
      const formData = new FormData(form);

      // Afficher les données du formulaire
      for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
      }

      // Soumettre le formulaire via AJAX
      console.log("Envoi de la requête AJAX à set_session.php");
      fetch('set_session.php', {
        method: 'POST',
        body: formData
      })
        .then(response => {
          console.log("Réponse reçue:", response.status);
          return response.text();
        })
        .then(data => {
          console.log("Données reçues:", data);
          console.log("Redirection vers association.php");
          // Rediriger
          window.location.href = 'association.php';
        })
        .catch(error => {
          console.error("Erreur AJAX:", error);
        });
    }



function checkProfileCompleteness(formData) {
  // Vérifier si tous les champs sont remplis
  const hasBio = document.getElementById('bio').value.trim().length > 10;
  const hasLocation = document.getElementById('locationCoordinates').value !== '';
  const hasName = document.getElementById('firstName') && document.getElementById('lastName');
  const hasPicture = document.getElementById('profilePic').src.indexOf('default.webp') === -1;
  
  // Si le profil est complet, attribuer des points
  if (hasBio && hasLocation && hasName && hasPicture) {
    // Attribuer des points pour avoir complété le profil
    fetch('../backend/xp.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'action=complete_profile&details=profil'
    })
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        showXPNotification(data.points, "Profil complété!");
      // Vérifier si l'utilisateur a monté de niveau
    if (data.level_up) {
      // Noms des niveaux (à synchroniser avec le backend)
      const level_names = {
        1: "Bénévole Débutant",
        2: "Bénévole Actif",
        3: "Bénévole Engagé",
        4: "Bénévole Expérimenté",
        5: "Bénévole Expert",
        6: "Bénévole Maître",
        7: "Bénévole Émérite",
        8: "Bénévole Légendaire",
        9: "Bénévole Héroïque",
        10: "Bénévole Mythique"
      };
      
      // Afficher la notification de montée de niveau
      const levelName = level_names[data.new_level] || "Niveau " + data.new_level;
      setTimeout(() => {
        showLevelUpNotification(data.new_level, levelName);
      }, 1500); // Montrer après la notification XP
    }
  }
})
    .catch(error => console.error('Erreur XP:', error));
  } else {
    // Attribuer des points pour la mise à jour du profil
    fetch('../backend/xp.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'action=update_profile&details=profil'
    })
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        showXPNotification(data.points, "Profil mis à jour!");
      // Vérifier si l'utilisateur a monté de niveau
    if (data.level_up) {
      // Noms des niveaux (à synchroniser avec le backend)
      const level_names = {
        1: "Bénévole Débutant",
        2: "Bénévole Actif",
        3: "Bénévole Engagé",
        4: "Bénévole Expérimenté",
        5: "Bénévole Expert",
        6: "Bénévole Maître",
        7: "Bénévole Émérite",
        8: "Bénévole Légendaire",
        9: "Bénévole Héroïque",
        10: "Bénévole Mythique"
      };
      
      // Afficher la notification de montée de niveau
      const levelName = level_names[data.new_level] || "Niveau " + data.new_level;
      setTimeout(() => {
        showLevelUpNotification(data.new_level, levelName);
      }, 1500); // Montrer après la notification XP
    }
  }
})
    .catch(error => console.error('Erreur XP:', error));
  }
}
  </script>
  <script>
    // Créez une fonction pour afficher une notification quand l'utilisateur gagne de l'XP
function showXPNotification(points, reason) {
  const notification = document.createElement('div');
  notification.className = 'fixed bottom-4 right-4 bg-gradient-to-r from-primary to-secondary text-white py-2 px-4 rounded-lg shadow-lg z-50 transform transition-all duration-500 translate-y-20 opacity-0';
  notification.innerHTML = `
    <div class="flex items-center">
      <div class="mr-3 bg-white rounded-full p-2">
        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
      </div>
      <div>
        <p class="font-medium">+${points} XP</p>
        <p class="text-sm text-white-200">${reason}</p>
      </div>
    </div>
  `;
  
  document.body.appendChild(notification);
  
  // Animation d'entrée
  setTimeout(() => {
    notification.classList.remove('translate-y-20', 'opacity-0');
  }, 100);
  
  // Animation de sortie
  setTimeout(() => {
    notification.classList.add('translate-y-20', 'opacity-0');
    setTimeout(() => {
      notification.remove();
    }, 500);
  }, 4000);
}
  </script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Éléments pour la modal de désabonnement
    const unfollowModal = document.getElementById('unfollowModal');
    const cancelUnfollow = document.getElementById('cancelUnfollow');
    const confirmUnfollow = document.getElementById('confirmUnfollow');
    const unfollowAssociationId = document.getElementById('unfollowAssociationId');
    
    // Gestionnaires d'événements pour les boutons "Ne plus suivre"
    document.querySelectorAll('.unfollow-btn').forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        const associationId = this.getAttribute('data-association-id');
        unfollowAssociationId.value = associationId;
        unfollowModal.classList.remove('hidden');
      });
    });
    
    // Fermer la modal quand on clique sur Annuler
    if (cancelUnfollow) {
      cancelUnfollow.addEventListener('click', function() {
        unfollowModal.classList.add('hidden');
      });
    }
    
    // Confirmer le désabonnement
    if (confirmUnfollow) {
      confirmUnfollow.addEventListener('click', function() {
        const associationId = unfollowAssociationId.value;
        if (!associationId) return;
        
        // Créer les données du formulaire
        const formData = new FormData();
        formData.append('association_id', associationId);
        
        // Envoyer la requête au serveur
        fetch('unfollow_association.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            // Si succès, recharger la page
            window.location.reload();
          } else {
            // Sinon, afficher l'erreur
            alert('Erreur: ' + data.message);
            unfollowModal.classList.add('hidden');
          }
        })
        .catch(error => {
          console.error('Erreur:', error);
          alert('Une erreur est survenue lors du désabonnement');
          unfollowModal.classList.add('hidden');
        });
      });
    }
    
    // Fermer la modal si on clique en dehors
    if (unfollowModal) {
      unfollowModal.addEventListener('click', function(e) {
        if (e.target === unfollowModal) {
          unfollowModal.classList.add('hidden');
        }
      });
    }
  });
</script>
</body>

</html>