<?php
session_start();
include '../backend/db.php';

header('Content-Type: application/json');

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Utilisateur non connecté']);
    exit;
}

// Récupérer les paramètres
$lat = isset($_POST['lat']) ? floatval($_POST['lat']) : null;
$lon = isset($_POST['lon']) ? floatval($_POST['lon']) : null;
$distance = isset($_POST['distance']) ? intval($_POST['distance']) : 50;
$sort = isset($_POST['sort']) ? $_POST['sort'] : 'closest';
$tags = isset($_POST['tags']) ? json_decode($_POST['tags'], true) : [];

// Vérifier que les coordonnées sont présentes
if ($lat === null || $lon === null) {
    echo json_encode(['error' => 'Coordonnées manquantes']);
    exit;
}

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

// Requête SQL pour récupérer toutes les missions avec leurs associations
$sql = "SELECT m.*, a.association_name, a.association_adress 
        FROM missions m 
        JOIN association a ON m.association_id = a.association_id";

$result = $conn->query($sql);

$missions = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Déterminer les coordonnées à utiliser (mission ou association)
        $mission_coords = null;

        // Vérifier si la mission a sa propre localisation
        if (!empty($row['location'])) {
            $location = json_decode($row['location'], true);
            if (isset($location['coordinates'])) {
                $mission_coords = $location['coordinates'];
            }
        }

        // Sinon, utiliser l'adresse de l'association
        if (!$mission_coords && !empty($row['association_adress'])) {
            $association_adress = json_decode($row['association_adress'], true);
            if (isset($association_adress['coordinates'])) {
                $mission_coords = $association_adress['coordinates'];
            }
        }

        // Si on n'a pas de coordonnées, ignorer cette mission
        if (!$mission_coords) {
            continue;
        }

        // Calculer la distance
        $mission_lat = $mission_coords[1];
        $mission_lon = $mission_coords[0];
        $distance_km = haversine($lat, $lon, $mission_lat, $mission_lon);

        // Filtrer par distance
        if ($distance_km <= $distance) {
            // Décoder les tags pour le filtrage
            $mission_tags = !empty($row['tags']) ? json_decode($row['tags'], true) : [];

            // Filtrer par tags si des tags sont sélectionnés
            if (!empty($tags)) {
                $has_tag = false;
                foreach ($tags as $tag) {
                    if (in_array($tag, $mission_tags)) {
                        $has_tag = true;
                        break;
                    }
                }
                if (!$has_tag) {
                    continue;
                }
            }

            $row['distance'] = $distance_km;
            $row['coordinates'] = $mission_coords;
            $missions[] = $row;
        }
    }
}

// Trier les résultats
if (!empty($missions)) {
    if ($sort === 'closest') {
        usort($missions, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
    } elseif ($sort === 'recent') {
        usort($missions, function ($a, $b) {
            return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        });
    } elseif ($sort === 'volunteers_needed') {
        usort($missions, function ($a, $b) {
            $a_remaining = intval($a['volunteers_needed']) - intval($a['volunteers_registered']);
            $b_remaining = intval($b['volunteers_needed']) - intval($b['volunteers_registered']);
            return $b_remaining <=> $a_remaining;
        });
    }
}

echo json_encode($missions);
