<!DOCTYPE html>
<html lang="en">

<?php

include '../backend/db.php';
session_start();

if (!isset($_SESSION['user_mail'])) {
    // header('Location: signup.php');

}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HeartHive</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <h1>HeartHive</h1>

    <?php
    // Récupérer les données des utilisateurs
    $sql = "SELECT user_name, user_firstname, user_adress, user_mail, user_date, user_profile_picture FROM user";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table border='1'>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Adresse</th>
                    <th>Email</th>
                    <th>Date de naissance</th>
                    <th>Photo de profil</th>
                </tr>";
        // Afficher les données de chaque utilisateur
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['user_name']) . "</td>
                    <td>" . htmlspecialchars($row['user_firstname']) . "</td>
                    <td>" . htmlspecialchars($row['user_adress']) . "</td>
                    <td>" . htmlspecialchars($row['user_mail']) . "</td>
                    <td>" . htmlspecialchars($row['user_date']) . "</td>
                    <td><img src='" . htmlspecialchars($row['user_profile_picture']) . "' alt='Profile Picture' width='50' height='50'></td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "Aucun utilisateur trouvé.";
    }

    $conn->close();
    ?>

    <script type="module" src="js/script.js"></script>
</body>

</html>