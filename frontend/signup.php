<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.css" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/signup.css" />
  <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon" />

  <!-- Parsley CSS (optionnel) -->
  <style>
    .parsley-errors-list {
      list-style: none;
      padding: 0;
      margin: 5px 0 0 0;
      color: #dc3545;
      font-size: 0.9em;
    }

    .input-error {
      border-color: #dc3545 !important;
    }
  </style>

  <title>Inscription - HeartHive</title>
</head>

<body>



  <style>
    .choice {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-bottom: -100px;
    }

    .choice input[type="radio"] {
      display: none;
    }

    .choice .toggle {
      padding: 10px 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s, color 0.3s;
    }

    .choice input[type="radio"]:checked+.toggle {
      background-color: #007bff;
      color: white;
      border-color: #007bff;
    }
  </style>
  <form
    action="../backend/traitement_formulaire.php"
    method="POST"
    class="form"
    id="multiStepForm"
    data-parsley-validate
    enctype="multipart/form-data">
    <h1 class="text-center">S'inscrire</h1>

    <!-- Progress bar -->
    <div class="progressbar">
      <div class="progress" id="progress"></div>
      <div
        class="progress-step progress-step-active"
        data-title="Information"></div>
      <div class="progress-step" data-title="Localisation"></div>
      <div class="progress-step" data-title="Contact"></div>
    </div>

    <!-- Étape 1 -->
    <div class="form-step form-step-active" data-parsley-group="step1">
      <h2>Bienvenue !</h2>
      <p>
        Avec HeartHive, trouvez du sens, faites des rencontres et engagez-vous
        dans des missions solidaires près de chez vous !
      </p>

      <div class="input-group">
        <label for="surname" id="surname-label">Nom</label>
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            const particulierRadio = document.getElementById('particulier');
            const associationRadio = document.getElementById('association');
            const surnameLabel = document.getElementById('surname-label');
            const nameLabel = document.querySelector('label[for="name"]');
            const birthLabel = document.querySelector('label[for="birth"]');

            particulierRadio.addEventListener('change', function() {
              if (particulierRadio.checked) {
                surnameLabel.textContent = 'Nom';
                nameLabel.textContent = 'Prénom';
                birthLabel.textContent = 'Date de naissance';
              }
            });

            associationRadio.addEventListener('change', function() {
              if (associationRadio.checked) {
                surnameLabel.textContent = "Nom de l'association";
                nameLabel.textContent = 'Numéro de SIRET';
                birthLabel.textContent = "Date de création de l'association";
              }
            });
          });
        </script>
        <input
          type="text"
          id="surname"
          name="surname"
          data-parsley-required="true"
          data-parsley-group="step1"
          data-parsley-trigger="change"
          data-parsley-error-message="Veuillez saisir votre nom" />
      </div>

      <div class="input-group">
        <label for="name">Prénom</label>
        <input
          type="text"
          id="name"
          name="name"
          data-parsley-required="true"
          data-parsley-group="step1"
          data-parsley-trigger="change"
          data-parsley-error-message="Veuillez saisir votre prénom" />
      </div>

      <div class="input-group">
        <label for="birth">Date de naissance</label>
        <input
          type="date"
          id="birth"
          name="birth"
          data-parsley-required="true"
          data-parsley-group="step1"
          data-parsley-trigger="change"
          data-parsley-error-message="Veuillez saisir une date valide, vous devez avoir plus de 18 ans pour vous inscrire"
          data-parsley-age />
      </div>

      <div class="btns-group">
        <button type="button" class="btn btn-next">Suivant</button>
      </div>
    </div>

    <!-- Étape 2 -->
    <div class="form-step" data-parsley-group="step2">
      <div class="input-group">
        <label for="city-search">Où voulez-vous contribuer ?</label>
        <div style="display: flex; align-items: center">
          <input
            type="text"
            id="city-search"
            name="city"
            data-parsley-group="step2"
            data-parsley-required="true"
            data-parsley-trigger="change"
            data-parsley-error-message="Veuillez sélectionner une ville valide" />
          <span id="validation-icon" class="validation-icon"></span>
        </div>
        <div id="suggestions" class="suggestions"></div>
      </div>

      <p>
        <span>Dans un rayon de </span>
        <span id="nb-km">0 km</span>
      </p>
      <input
        type="range"
        id="radius-range"
        min="0"
        max="8"
        value="0"
        step="1"
        name="range" />
      <input type="hidden" id="city-data" name="city-data" />

      <div id="map" style="height: 300px; width: 100%"></div>

      <div class="btns-group">
        <button type="button" class="btn btn-prev">Précédent</button>
        <button type="button" class="btn btn-next">Suivant</button>
      </div>
    </div>

    <!-- Étape 3 -->
    <div class="form-step" data-parsley-group="step3">
      <div class="input-group">
        <label for="profile-pic">Photo de profil</label>
        <input
          type="file"
          id="profile-pic"
          name="profile-pic"
          accept="image/*"
          style="display: none"
          hidden />

        <div class="profile_center">
          <div
            class="profile-pic-container"
            onclick="document.getElementById('profile-pic').click();">
            <img
              id="profile-pic-preview"
              src="assets/uploads/profile_pictures/default.webp"
              alt="Photo de profil" />
            <span class="upload-text">Cliquez pour ajouter une photo</span>
          </div>
        </div>
      </div>

      <div class="input-group">
        <label for="password">Mot de passe</label>
        <input
          type="password"
          id="password"
          name="password"
          data-parsley-required="true"
          data-parsley-group="step3"
          data-parsley-minlength="8"
          data-parsley-trigger="change"
          data-parsley-error-message="Le mot de passe doit contenir au moins 8 caractères" />
      </div>

      <div class="input-group">
        <label for="confirmPassword">Confirmer le mot de passe</label>
        <input
          type="password"
          id="confirmPassword"
          name="confirmPassword"
          data-parsley-required="true"
          data-parsley-group="step3"
          data-parsley-equalto="#password"
          data-parsley-trigger="change"
          data-parsley-error-message="Les mots de passe ne correspondent pas" />
      </div>
      <div class="input-group">
        <label for="email">Adresse Mail</label>
        <input
          type="email"
          id="email"
          name="email"
          data-parsley-required="true"
          data-parsley-group="step3"
          data-parsley-trigger="change"
          data-parsley-error-message="Veuillez saisir votre adresse e-mail" />
      </div>

      <div class="btns-group">
        <button type="button" class="btn btn-prev">Précédent</button>
        <button type="submit" class="btn">S'inscrire</button>
      </div>
    </div>
  </form>

  <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>
  <script src="js/leaflet-signup.js"></script>
</body>

</html>