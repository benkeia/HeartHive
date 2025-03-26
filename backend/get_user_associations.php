<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
    exit;
}

$user_id = $_SESSION['user_id'];
$response = ['status' => 'success', 'associations' => []];

try {
    // Récupérer les engagements de l'utilisateur
    $query = "SELECT a.*, ua.status, ua.join_date 
              FROM association a 
              JOIN user_association ua ON a.association_id = ua.association_id 
              WHERE ua.user_id = ? ORDER BY ua.join_date DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($asso = $result->fetch_assoc()) {
            $response['associations'][] = $asso;
        }
    }

    $stmt->close();
    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>