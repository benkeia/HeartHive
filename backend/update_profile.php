<?php
// Désactiver l'affichage des erreurs PHP pour la production
error_reporting(0);
ini_set('display_errors', 0);

// Spécifier que la réponse est du JSON
header('Content-Type: application/json');

session_start();
include 'db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$response = ['status' => 'error', 'message' => 'No changes made'];

// Vérifier si des données ont été envoyées
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [];
    $params = [];
    $types = '';
    
    // Traiter nom et prénom
    if (isset($_POST['firstName']) && isset($_POST['lastName'])) {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        
        $updates[] = "user_firstname = ?";
        $params[] = $firstName;
        $types .= 's';
        
        $updates[] = "user_name = ?";
        $params[] = $lastName;
        $types .= 's';
    }
    
    // Traiter la biographie
    if (isset($_POST['bio']) && !empty($_POST['bio'])) {
        $bio = $_POST['bio'];
        
        $updates[] = "user_bio = ?";
        $params[] = $bio;
        $types .= 's';
    }
    
    // Traiter les données de localisation
    if (isset($_POST['location_data']) && !empty($_POST['location_data'])) {
        $locationData = $_POST['location_data'];
        
        $updates[] = "user_adress = ?";
        $params[] = $locationData;
        $types .= 's';
    }
    
    // Si des mises à jour sont à faire
    if (count($updates) > 0) {
        $query = "UPDATE user SET " . implode(', ', $updates) . " WHERE user_id = ?";
        $params[] = $user_id;
        $types .= 'i';
        
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            echo json_encode(['status' => 'error', 'message' => 'Prepare statement failed: ' . $conn->error]);
            exit;
        }
        
        // Lier les paramètres dynamiquement
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            // Mettre à jour les données de session
            if (isset($_POST['firstName'])) $_SESSION['user_firstname'] = $_POST['firstName'];
            if (isset($_POST['lastName'])) $_SESSION['user_name'] = $_POST['lastName'];
            if (isset($_POST['bio'])) $_SESSION['user_bio'] = $_POST['bio'];
            if (isset($_POST['location_data'])) $_SESSION['user_adress'] = $_POST['location_data'];
            
            $response = ['status' => 'success', 'message' => 'Profile updated successfully'];
        } else {
            $response = ['status' => 'error', 'message' => 'Database update failed: ' . $stmt->error];
        }
        
        $stmt->close();
    }
}

echo json_encode($response);
?>