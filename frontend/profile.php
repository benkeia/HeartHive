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

#cropperModal > div {
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

#cropperModal.hidden > div {
  transform: translateY(20px);
}
  </style>
</head>

<body class="bg-light-bg font-sans">

  <?php include 'include/header.php'; ?>

  <!-- Ajout des données de tags pour JS -->
  <div id="userTagsData" data-tags='<?php echo htmlspecialchars($userTags, ENT_QUOTES, 'UTF-8'); ?>' class="hidden">
  </div>


  <div class="flex flex-col md:flex-row gap-6">
    <!-- Menu latéral -->
    <div class="w-full md:w-1/4 bg-white rounded-xl shadow-custom overflow-hidden">
      <div class="p-5">
        <h3 class="text-xl font-bold mb-6 text-gray-800">Mon compte</h3>
        <ul class="space-y-1">
          <li class="menu-item active p-3 rounded-lg text-gray-700">Mon profil</li>
          <li class="menu-item p-3 rounded-lg text-gray-700">Mes engagements</li>
          <li class="menu-item p-3 rounded-lg text-gray-700">Statistiques</li>
          <li class="menu-item p-3 rounded-lg text-gray-700">Certifications</li>
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0z">
                </path>
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
            id="location" placeholder="Entrez une ville..." 
            value="<?php 
              if (isset($_SESSION['user_adress']) && $_SESSION['user_adress'] !== '') {
                $address = json_decode($_SESSION['user_adress'], true);
                echo isset($address['name']) ? $address['name'] : '';
              }
            ?>" />
          <div id="locationResults" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden"></div>
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
      ?>" 
        class="w-full" id="locationRange" />
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
        id="bio" name="bio" rows="3"><?php echo isset($_SESSION['user_bio']) ? $_SESSION['user_bio'] : ''; ?></textarea>
    </div>
    <div>
  <label for="profilePicInput" class="block text-gray-700 font-medium mb-2">Photo de Profil</label>
  <input type="file" class="hidden" id="profilePicInput" name="profile_picture" accept="image/*">
  
  <div class="flex items-center space-x-4 mb-4">
    <div class="relative">
      <img id="previewImage" src="<?php echo $_SESSION['user_profile_picture'] ?>" class="w-24 h-24 rounded-full object-cover border-4 border-purple-100">
    </div>
    <button type="button" id="selectImageBtn" class="gradient-btn">
      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
      </svg>
      Changer la photo
    </button>
  </div>
  <input type="hidden" id="croppedImageData" name="cropped_image">
</div>
</div>
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
<div id="cropperModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden overflow-auto py-4">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl mx-4 flex flex-col max-h-[90vh]">
    <!-- En-tête de la popup -->
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
      <h3 class="font-semibold text-lg text-gray-800">Recadrer votre photo de profil</h3>
      <button id="closeModal" class="text-gray-400 hover:text-gray-500">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
    
    <!-- Corps de la popup avec défilement -->
    <div class="p-6 overflow-auto flex-grow" style="max-height: calc(90vh - 160px);">
      <div class="mb-6">
        <img id="cropperImageModal" class="max-w-full mx-auto">
      </div>
      <p class="text-sm text-gray-500 mb-4 text-center">Faites glisser la zone de sélection pour recadrer votre image.</p>
    </div>
    
    <!-- Pied de la popup (toujours visible) -->
    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 sticky bottom-0">
      <button id="cancelCropBtnModal" class="bg-gray-100 text-gray-700 border border-gray-300 py-2 px-6 rounded-lg font-medium hover:bg-gray-200 transition">
        Annuler
      </button>
      <button id="cropBtnModal" class="gradient-btn py-2 px-6">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        Appliquer
      </button>
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

        <!-- Bloc des disponibilités -->
        <div class="w-full lg:w-1/2">
          <?php include 'include/update_availability.php'; ?>

        </div>
      </div>
    </div>
  </div>
  </div>


  <script>
    // Activer le premier élément du menu par défaut
    document.querySelector('.menu-item').classList.add('active');
  </script>
  <script>
document.addEventListener('DOMContentLoaded', function() {
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
    selectImageBtn.addEventListener('click', function() {
      profilePicInput.click();
    });
  }
  
  // Initialiser le cropper quand une image est sélectionnée
  if (profilePicInput) {
    profilePicInput.addEventListener('change', function(e) {
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
        ready: function() {
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
    cancelCropBtnModal.addEventListener('click', function() {
      closeCropperModal();
      // Réinitialiser l'input de fichier
      profilePicInput.value = '';
    });
  }
  
  // Appliquer le recadrage quand on clique sur Appliquer
  if (cropBtnModal) {
    cropBtnModal.addEventListener('click', function() {
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
  cropperModal.addEventListener('click', function(e) {
    if (e.target === cropperModal) {
      closeCropperModal();
    }
  });
  
  // Fermer la popup avec la touche Echap
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !cropperModal.classList.contains('hidden')) {
      closeCropperModal();
    }
  });

  // ... Reste de votre code JavaScript existant ...
});






// Ajoutez ce code à la fin de votre script existant
document.addEventListener('DOMContentLoaded', function() {
  // Gestionnaire du champ de localisation
  const locationInput = document.getElementById('location');
  const locationResults = document.getElementById('locationResults');
  const locationCoordinates = document.getElementById('locationCoordinates');
  const locationRange = document.getElementById('locationRange');
  const rangeValue = document.getElementById('rangeValue');
  
  // Afficher la valeur du rayon
  if (locationRange && rangeValue) {
    locationRange.addEventListener('input', function() {
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
    
    locationInput.addEventListener('input', function() {
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
    document.addEventListener('click', function(e) {
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
        
        div.addEventListener('click', function() {
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
    nameInput.addEventListener('blur', function() {
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
  profileForm.addEventListener('submit', function(e) {
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
        alert('Profil mis à jour avec succès');
        // Recharger la page ou mettre à jour l'affichage
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

</body>

</html>