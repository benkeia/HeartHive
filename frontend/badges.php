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

                    <h1>Récompenses</h1>

                    <div class="badges">
                        <div class="badge">
                            <img src="assets/img/badge1.png" alt="badge1" />
                            <p>Badge 1</p>
                        </div>
                        <div class="badge">
                            <img src="assets/img/badge2.png" alt="badge2" />
                            <p>Badge 2</p>
                        </div>
                        <div class="badge">
                            <img src="assets/img/badge3.png" alt="badge3" />
                            <p>Badge 3</p>
                        </div>
                        <div class="badge">
                            <img src="assets/img/badge4.png" alt="badge4" />
                            <p>Badge 4</p>
                        </div>
                        <div class="badge">
                            <img src="assets/img/badge5.png" alt="badge5" />
                            <p>Badge 5</p>
                        </div>
                        <div class="badge">
                            <img src="assets/img/badge6.png" alt="badge6" />
                            <p>Badge 6</p>
                        </div>
                        <div class="badge">
                            <img src="assets/img/badge7.png" alt="badge7" />
                            <p>Badge 7</p>
                        </div>
                    </div>


                </div>
            </div>
        </div>
</body>

</html>