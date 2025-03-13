<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylelogin.css">
    <title>Page de connexion</title>
</head>
<body>
<?php
include '../backend/db.php';
?>
<div id="container">
    <h1>Connexion au site</h1>
    <form action="login.php" method="post">
        <label for="mail">Entrez votre email :</label>
        <input type="email" id="mail" name="mail" placeholder="mail" required>
        <br>
        <label for="password">Entrez votre mot de passe :</label>
        <input type="password" id="password" name="password" placeholder="mot de passe" required>
        <br>
        <input type="submit" value="Valider">
    </form>
</div>

</body>
</html>