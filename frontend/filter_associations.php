<?php
include '../backend/db.php';

$lat = $_POST['lat'] ?? null;
$lon = $_POST['lon'] ?? null;
$distance = $_POST['distance'] ?? 10; // Distance par défaut
$sort = $_POST['sort'] ?? 'closest';
$user_city = isset($user_adress['city']) ? $user_adress['city'] : '';

if (!$lat || !$lon) {
    echo json_encode([]);
    exit;
}

// Fonction pour calculer la distance (Haversine)
function haversine($lat1, $lon1, $lat2, $lon2) {
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
$sql = "SELECT association_id, association_name, association_adress, association_mission FROM association";
$result = $conn->query($sql);

$associations = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $association_adress = json_decode($row['association_adress'], true);
        if (!isset($association_adress['coordinates'])) continue;

        $assoc_lat = $association_adress['coordinates'][1];
        $assoc_lon = $association_adress['coordinates'][0];
        $dist = haversine($lat, $lon, $assoc_lat, $assoc_lon);

        if ($dist <= $distance) {
            $row['distance'] = $dist;
            $associations[] = $row;
        }
    }

    // Trier les résultats
    if ($sort === 'closest') {
        usort($associations, fn($a, $b) => $a['distance'] <=> $b['distance']);
    }
}

echo json_encode($associations);
?>
