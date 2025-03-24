<?php
include 'db.php';

// Vérification de la session
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

// Récupération des données POST
$postData = json_decode(file_get_contents('php://input'), true);

if (!isset($postData['type']) || !isset($postData['tags'])) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

$type = $postData['type']; // 'interests' ou 'skills'
$tags = $postData['tags']; // Tableau des tags

// Récupération des tags actuels de l'utilisateur
$userId = $_SESSION['user_id'];
$query = "SELECT user_tags FROM user WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur introuvable']);
    exit;
}

$row = $result->fetch_assoc();
$currentTags = json_decode($row['user_tags'] ?? '{}', true) ?: [];

// Mise à jour des tags spécifiés (intérêts ou compétences)
$currentTags[$type] = $tags;

// Sauvegarde des tags mis à jour dans la base de données
$updatedTags = json_encode($currentTags, JSON_UNESCAPED_UNICODE);
$updateQuery = "UPDATE user SET user_tags = ? WHERE user_id = ?";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bind_param('si', $updatedTags, $userId);

if ($updateStmt->execute()) {
    // Mise à jour de la session
    $_SESSION['user_tags'] = $updatedTags;
    echo json_encode(['success' => true, 'message' => 'Tags mis à jour avec succès']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour: ' . $conn->error]);
}

$updateStmt->close();
$stmt->close();
$conn->close();
