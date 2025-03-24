<?php

include '../backend/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
  <script src="js/codeProfile.js"></script>
  <title>Profile</title>
  <style>
    .hour-cell {
      cursor: pointer;
      height: 40px;
    }

    .hour-cell.selected {
      background-color: #4caf50;
    }
  </style>
</head>

<body class="bg-gray-100 font-sans">

  <?php include 'include/header.php'; ?>

  <div class="container mx-auto mt-10">
    <div class="flex">
      <div class="w-1/4 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-semibold mb-4">Mon compte</h3>
        <ul class="space-y-2">
          <li class="hover:bg-gray-100 p-2 rounded cursor-pointer">Mon profil</li>
          <hr />
          <li class="hover:bg-gray-100 p-2 rounded cursor-pointer">Mes engagements</li>
          <hr />
          <li class="hover:bg-gray-100 p-2 rounded cursor-pointer">Statistiques</li>
          <hr />
          <li class="hover:bg-gray-100 p-2 rounded cursor-pointer">Certifications</li>
          <hr />
          <li class="hover:bg-gray-100 p-2 rounded cursor-pointer">Messagerie</li>
          <hr />
          <li class="hover:bg-gray-100 p-2 rounded cursor-pointer">Paramètres</li>
          <hr />
        </ul>
      </div>
      <div class="w-3/4 ml-6">
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center space-x-4">
            <img id="profilePic" src="<?php echo $_SESSION['user_profile_picture'] ?>" alt="Photo de profil" class="h-24 w-24 rounded-full border-4 border-gray-300 object-cover" />
            <div>
              <h2 id="profileName" class="text-2xl font-semibold"><?php echo $_SESSION['firstname'] . ' ' . $_SESSION['name']; ?></h2>
              <h4 id="profileLocation" class="text-gray-600"><?php $address = json_decode($_SESSION['user_adress'], true);
                                                              echo $address['name']; ?></h4>
              <p id="profileBio" class="mt-2 text-gray-700"><?php if (isset($_SESSION['user_bio'])) {
                                                              echo $_SESSION['user_bio'];
                                                            } ?></p>
            </div>
          </div>

          <!-- Bouton Modifier -->
          <button id="editProfileBtn" class="mt-4 bg-purple-500 text-white py-2 px-4 rounded-lg hover:bg-purple-600 transition">Modifier</button>

          <!-- Formulaire de modification caché -->
          <div id="editProfileForm" class="hidden mt-6">
            <h3 class="text-xl font-semibold mb-4">Modifier le profil</h3>
            <form id="profileForm">
              <div class="mb-4">
                <label for="name" class="block text-gray-700">Nom Prénom</label>
                <input type="text" class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" id="name" value="Nom Prénom" />
              </div>
              <div class="mb-4">
                <label for="location" class="block text-gray-700">Localisation</label>
                <input type="text" class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" id="location" value="Location" />
              </div>
              <div class="mb-4">
                <label for="bio" class="block text-gray-700">Biographie</label>
                <textarea class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" id="bio" rows="3">Biographie</textarea>
              </div>
              <div class="mb-4">
                <label for="profilePicInput" class="block text-gray-700">Photo de Profil</label>
                <input type="file" class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" id="profilePicInput" />
              </div>
              <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition">Enregistrer</button>
              <button type="button" id="cancelEdit" class="bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition">Annuler</button>
            </form>
          </div>

          <div class="flex mt-10">
            <div class="w-1/2 pr-4">
              <div class="mb-6">
                <h2 class="text-xl font-semibold mb-4">Mes centres d'intérêts</h2>
                <div id="interestsContainer" class="flex flex-wrap gap-2">
                  <!-- Boutons cochables apparaissent ici -->
                </div>
                <button id="addInterestBtn" class="mt-4 bg-gradient-to-r from-pink-500 to-purple-500 text-white py-2 px-4 rounded-full hover:from-pink-600 hover:to-purple-600 transition">+</button>
                <div id="interestMenu" class="hidden mt-4 bg-white rounded-lg shadow-md p-4">
                  <h3 class="text-lg font-semibold mb-2">Ajouter un centre d'intérêt</h3>
                  <ul id="interestList" class="space-y-2">
                    <!-- Liste des intérêts possibles -->
                  </ul>
                </div>
              </div>
              <div>
                <h2 class="text-xl font-semibold mb-4">Mes compétences</h2>
                <div id="skillsContainer" class="flex flex-wrap gap-2"></div>
                <button id="addSkillBtn" class="mt-4 bg-gradient-to-r from-pink-500 to-purple-500 text-white py-2 px-4 rounded-full hover:from-pink-600 hover:to-purple-600 transition">+</button>
                <div id="skillMenu" class="hidden mt-4 bg-white rounded-lg shadow-md p-4">
                  <h3 class="text-lg font-semibold mb-2">Ajouter une compétence</h3>
                  <ul id="skillList" class="space-y-2"></ul>
                </div>
              </div>
            </div>
            <div class="w-1/2 pl-4">
              <h2 class="text-xl font-semibold mb-4">Mes Disponibilités</h2>
              <?php include 'include/update_availability.php'; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const hourCells = document.querySelectorAll('.hour-cell');
      let isDragging = false;

      hourCells.forEach(cell => {
        cell.addEventListener('mousedown', () => {
          isDragging = true;
          cell.classList.toggle('selected');
        });

        cell.addEventListener('mouseover', () => {
          if (isDragging) {
            cell.classList.add('selected');
          }
        });

        cell.addEventListener('mouseup', () => {
          isDragging = false;
        });
      });

      document.addEventListener('mouseup', () => {
        isDragging = false;
      });
    });
  </script>

</body>

</html>