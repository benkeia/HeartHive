<!DOCTYPE html>
<html lang="en">

<?php

include '../backend/db.php';
session_start();

if (!isset($_SESSION['user_mail'])) {
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
    // Fonction pour calculer la distance entre deux points géographiques
    function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earth_radius = 6371; // Rayon de la Terre en kilomètres

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earth_radius * $c;
    }

    // Coordonnées de référence
    $ref_lat = 43.2803;
    $ref_lon = 5.3806;

    // Récupérer les données des utilisateurs
    $sql = "SELECT user_name, user_firstname, user_adress, user_mail, user_date, user_profile_picture FROM user";
    $result = $conn->query($sql);

    $users = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $user_adress = json_decode($row['user_adress'], true);
            $distance = haversine($ref_lat, $ref_lon, $user_adress['coordinates'][1], $user_adress['coordinates'][0]);
            $row['distance'] = $distance;
            $row['within_range'] = $distance <= $user_adress['range'] ? 'Oui' : 'Non';
            $users[] = $row;
        }

        // Trier les utilisateurs par distance
        usort($users, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        echo "<table border='1'>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Coordonnées</th>
                    <th>Distance (km)</th>
                    <th>Email</th>
                    <th>Date de naissance</th>
                    <th>Photo de profil</th>
                    <th>Dans le rayon</th>
                </tr>";
        // Afficher les données de chaque utilisateur
        foreach ($users as $user) {
            $user_adress = json_decode($user['user_adress'], true);
            $coordinates = implode(', ', $user_adress['coordinates']);
            echo "<tr>
                    <td>" . htmlspecialchars($user['user_name']) . "</td>
                    <td>" . htmlspecialchars($user['user_firstname']) . "</td>
                    <td>" . htmlspecialchars($coordinates) . "</td>
                    <td>" . htmlspecialchars(round($user['distance'], 2)) . "</td>
                    <td>" . htmlspecialchars($user['user_mail']) . "</td>
                    <td>" . htmlspecialchars($user['user_date']) . "</td>
                    <td><img src='" . htmlspecialchars($user['user_profile_picture']) . "' alt='Profile Picture' width='50' height='50'></td>
                    <td>" . htmlspecialchars($user['within_range']) . "</td>
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