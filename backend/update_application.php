<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Vérifier que l'utilisateur est connecté et est une association
if (!isset($_SESSION['user_id']) || $_SESSION['type'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

// Vérifier les données requises
if (!isset($_POST['application_id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

$application_id = intval($_POST['application_id']);
$status = $_POST['status'];

// Valider le statut
$valid_statuses = ['pending', 'accepted', 'rejected'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Statut invalide']);
    exit;
}

// Récupérer les informations actuelles sur la candidature
$stmt = $conn->prepare("SELECT volunteer_id, mission_id FROM applications WHERE application_id = ?");
$stmt->bind_param("i", $application_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Candidature non trouvée']);
    exit;
}

$application = $result->fetch_assoc();
$user_id = $application['volunteer_id'];
$mission_id = $application['mission_id'];

// Mettre à jour le statut
$update_stmt = $conn->prepare("UPDATE applications SET status = ?, response_date = NOW() WHERE application_id = ?");
$update_stmt->bind_param("si", $status, $application_id);

if ($update_stmt->execute()) {
    // Si le statut est accepté, incrémenter le nombre de bénévoles inscrits
    if ($status === 'accepted') {
        $update_mission = $conn->prepare("UPDATE missions SET volunteers_registered = volunteers_registered + 1 WHERE mission_id = ?");
        $update_mission->bind_param("i", $mission_id);
        $update_mission->execute();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Statut mis à jour avec succès',
        'user_id' => $user_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
}
