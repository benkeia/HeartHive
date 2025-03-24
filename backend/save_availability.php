<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

// Vérifier si la requête est bien en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupérer les données de disponibilité
$availabilities = isset($_POST['availabilities']) ? $_POST['availabilities'] : null;
$userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);

// Vérifier que les données sont présentes
if (empty($availabilities) || empty($userId)) {
    echo json_encode(['success' => false, 'message' => 'Données incomplètes']);
    exit;
}

// Valider le format JSON des disponibilités
$decodedData = json_decode($availabilities, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Format de données invalide']);
    exit;
}

// Préparer la requête SQL pour mettre à jour les disponibilités de l'utilisateur
$stmt = $conn->prepare("UPDATE user SET user_disponibility = ? WHERE user_id = ?");
$stmt->bind_param("si", $availabilities, $userId);

// Exécuter la requête
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Disponibilités enregistrées avec succès']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement : ' . $stmt->error]);
}

$stmt->close();
$conn->close();
