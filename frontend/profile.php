<?php

include '../backend/db.php';
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="css/styleHeader.css" rel="stylesheet" />
  <link href="css/styleProfile.css" rel="stylesheet" />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
    crossorigin="anonymous" />
  <script src="js/codeProfile.js"></script>
  <!-- FullCalendar CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css"
    rel="stylesheet" />
  <!-- FullCalendar JS -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

  <title>Profile</title>
</head>

<body>

  <?php include 'include/header.php'; ?>

  </div>

  <div class="container">
    <div class="row">
      <div class="col-3">
        <div class="monCompte">
          <h3>Mon compte</h3>
          <ul>
            <li>Mon profil</li>
            <hr />
            <li>Mes engagements</li>
            <hr />
            <li>Statistiques</li>
            <hr />
            <li>Certifications</li>
            <hr />
            <li>Messagerie</li>
            <hr />
            <li>Paramètres</li>
            <hr />
          </ul>
        </div>
      </div>
      <div class="col-9">
        <div class="monProfil">
          <img
            id="profilePic"
            src="<?php echo $_SESSION['user_profile_picture'] ?>"
            alt="Photo de profil" />
          <h2 id="profileName"><?php
                                echo $_SESSION['firstname'] . ' ' . $_SESSION['name']; ?>


            <h4 id="profileLocation"> <?php
                                      $address = json_decode($_SESSION['user_adress'], true);
                                      echo $address['name']; ?></h4>
          </h2>
          <p id="profileBio"><?php if (isset($_SESSION['user_bio'])) {
                                echo $_SESSION['user_bio'];
                              } ?></p>

          <!-- Bouton Modifier -->
          <button id="editProfileBtn" class="btn btn-primary">
            Modifier
          </button>

          <!-- Formulaire de modification caché -->
          <div id="editProfileForm">
            <h3>Modifier le profil</h3>
            <form id="profileForm">
              <div class="mb-3">
                <label for="name" class="form-label">Nom Prénom</label>
                <input
                  type="text"
                  class="form-control"
                  id="name"
                  value="Nom Prénom" />
              </div>
              <div class="mb-3">
                <label for="location" class="form-label">Localisation</label>
                <input
                  type="text"
                  class="form-control"
                  id="location"
                  value="Location" />
              </div>
              <div class="mb-3">
                <label for="bio" class="form-label">Biographie</label>
                <textarea class="form-control" id="bio" rows="3">
Biographie</textarea>
              </div>
              <div class="mb-3">
                <label for="profilePicInput" class="form-label">Photo de Profil</label>
                <input
                  type="file"
                  class="form-control"
                  id="profilePicInput" />
              </div>
              <button type="submit" class="btn btn-success">
                Enregistrer
              </button>
              <button type="button" id="cancelEdit" class="btn btn-secondary">
                Annuler
              </button>
            </form>
          </div>
          <div class="row">
            <div class="col-6">
              <div class="row">
                <div class="interest">
                  <h2>Mes centres d'interêts</h2>
                  <div id="interestsContainer">
                    <!-- Boutons cochables apparaissent ici -->
                  </div>
                  <button id="addInterestBtn">+</button>
                  <div id="interestMenu" class="hidden">
                    <h3>Ajouter un centre d'intérêt</h3>
                    <ul id="interestList">
                      <!-- Liste des intérêts possibles -->
                    </ul>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="comp">
                  <h2>Mes compétences</h2>
                  <div id="skillsContainer"></div>
                  <button id="addSkillBtn">+</button>

                  <div id="skillMenu" class="hidden">
                    <h3>Ajouter une compétence</h3>
                    <ul id="skillList"></ul>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-6">
              <div class="dispo">
                <h2>Mes Disponibilités</h2>
                <div id="calendar"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>