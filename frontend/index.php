<!DOCTYPE html>
<html lang="fr">

<?php
include '../backend/db.php';
session_start();

if (!isset($_SESSION['mail'])) {
    echo "Veuillez vous connecter pour voir les recommandations.";
    exit;
}

// Récupération des coordonnées de l'utilisateur
$user_mail = $_SESSION['mail'];
$sql_user = "SELECT user_adress FROM user WHERE user_mail = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("s", $user_mail);
$stmt->execute();
$result_user = $stmt->get_result();
$user_data = $result_user->fetch_assoc();

if (!$user_data || empty($user_data['user_adress'])) {
    echo "Coordonnées de l'utilisateur introuvables.";
    exit;
}

$user_adress = json_decode($user_data['user_adress'], true);
if (!isset($user_adress['coordinates']) || !isset($user_adress['range'])) {
    echo "Format d'adresse invalide.";
    exit;
}

$user_lat = $user_adress['coordinates'][1];
$user_lon = $user_adress['coordinates'][0];
$user_range = $user_adress['range']; // Rayon de recherche de l'utilisateur

function haversine($lat1, $lon1, $lat2, $lon2)
{
    $earth_radius = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earth_radius * $c;
}

// Récupérer les associations
$sql = "SELECT association_name, association_siren, association_adress, association_mail, association_date, 
               association_profile_picture, association_mission 
        FROM association";
$result = $conn->query($sql);

$associations = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $association_adress = json_decode($row['association_adress'], true);
        if (!isset($association_adress['coordinates'])) {
            continue;
        }

        $assoc_lat = $association_adress['coordinates'][1];
        $assoc_lon = $association_adress['coordinates'][0];
        $distance = haversine($user_lat, $user_lon, $assoc_lat, $assoc_lon);

        $row['distance'] = $distance;
        $row['within_range'] = $distance <= $user_range ? '✅ Oui' : '❌ Non';
        $associations[] = $row;
    }

    // Trier les associations par distance
    usort($associations, fn($a, $b) => $a['distance'] <=> $b['distance']);
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HeartHive - Recommandations</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <h1>HeartHive</h1>
    <p>Recommandations d'associations proches de vous (Rayon : <?= htmlspecialchars($user_range) ?> km) :</p>

    <?php if (!empty($associations)) : ?>
        <table border="1">
            <tr>
                <th>Nom</th>
                <th>SIREN</th>
                <th>Coordonnées</th>
                <th>Distance (km)</th>
                <th>Email</th>
                <th>Date de création</th>
                <th>Mission</th>
                <th>Photo</th>
                <th>Dans votre rayon</th>
            </tr>
            <?php foreach ($associations as $association) : ?>
                <?php 
                $association_adress = json_decode($association['association_adress'], true);
                $coordinates = implode(', ', $association_adress['coordinates']);
                ?>
                <tr>
                    <td><?= htmlspecialchars($association['association_name']) ?></td>
                    <td><?= htmlspecialchars($association['association_siren']) ?></td>
                    <td><?= htmlspecialchars($coordinates) ?></td>
                    <td><?= round($association['distance'], 2) ?> km</td>
                    <td><?= htmlspecialchars($association['association_mail']) ?></td>
                    <td><?= htmlspecialchars($association['association_date']) ?></td>
                    <td><?= htmlspecialchars($association['association_mission']) ?></td>
                    <td><img src="<?= htmlspecialchars($association['association_profile_picture']) ?>" alt="Photo" width="50"></td>
                    <td><?= $association['within_range'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else : ?>
        <p>Aucune association trouvée.</p>
    <?php endif; ?>

    <script type="module" src="../node_modules/dropzone/dist/dropzone-min.js"></script>
    <script type="module" src="js/script.js"></script>
</body>

</html>
