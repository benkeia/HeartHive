<?php

session_start();

include '../backend/db.php';

if (isset($_SESSION['authentification']) && $_SESSION['authentification'] == true) {
    header('Location: profile.php');
}

?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/alerts.css">
    <title>Page de connexion</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <?php
    include '../backend/db.php';
    ?>
    <div id="container">
        <h1>Connexion au site</h1>
        <div id="error-message" class="alert alert-error"></div>
        <form id="login-form" action="" method="post" class="form">
            <label for="mail">Entrez votre email :</label>
            <input type="email" id="mail" name="mail" placeholder="mail" required>
            <label for="password">Entrez votre mot de passe :</label>
            <input type="password" id="password" name="password" placeholder="mot de passe" required>
            <input type="submit" value="Valider" class="btn">
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('#login-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: 'login.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.redirect;
                        } else {
                            $('#error-message').text(response.message).show();
                        }
                    },
                    error: function() {
                        $('#error-message').text('Une erreur est survenue').show();
                    }
                });
            });
        });
    </script>
</body>

</html>