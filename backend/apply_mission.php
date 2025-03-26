<?php
session_start();
require_once 'db.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

// Vérifier que les données requises sont présentes
if (!isset($_POST['mission_id']) || !isset($_POST['volunteer_id']) || !isset($_POST['motivation'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

$mission_id = intval($_POST['mission_id']);
$volunteer_id = intval($_POST['volunteer_id']);
$motivation = $_POST['motivation'];
$availability = $_POST['availability'] ?? '';

// Vérifier que l'utilisateur est bien celui qui est connecté
if ($volunteer_id !== $_SESSION['user_id']) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit;
}

// Vérifier si la mission existe et a encore des places
$stmt = $conn->prepare("SELECT * FROM missions WHERE mission_id = ?");
$stmt->bind_param("i", $mission_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Mission introuvable']);
    exit;
}

$mission = $result->fetch_assoc();
$places_remaining = $mission['volunteers_needed'] - ($mission['volunteers_registered'] ?? 0);

if ($places_remaining <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Il n\'y a plus de places disponibles pour cette mission']);
    exit;
}

// Vérifier si l'utilisateur a déjà postulé
$stmt = $conn->prepare("SELECT * FROM applications WHERE mission_id = ? AND volunteer_id = ?");
$stmt->bind_param("ii", $mission_id, $volunteer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Vous avez déjà postulé à cette mission']);
    exit;
}

// Insérer la candidature
try {
    $stmt = $conn->prepare("INSERT INTO applications (mission_id, volunteer_id, motivation, availability, application_date, status) VALUES (?, ?, ?, ?, NOW(), 'pending')");
    $stmt->bind_param("iiss", $mission_id, $volunteer_id, $motivation, $availability);
    $stmt->execute();

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Candidature envoyée avec succès']);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement de la candidature: ' . $e->getMessage()]);
}
