<?php
session_start();
include '../backend/db.php';

header('Content-Type: application/json');

if (isset($_POST['association_id']) && isset($_SESSION['user_id'])) {
    $association_id = $_POST['association_id'];
    $user_id = $_SESSION['user_id'];

    // Vérifier si la postulation existe
    $check_query = "SELECT * FROM postulation WHERE postulation_association_id_fk = ? AND postulation_user_id_fk = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $association_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Vous ne suivez pas cette association."]);
    } else {
        // Supprimer la postulation
        $query = "DELETE FROM postulation WHERE postulation_association_id_fk = ? AND postulation_user_id_fk = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $association_id, $user_id);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success", 
                "message" => "Vous ne suivez plus cette association.",
                "xp_added" => false,
                "xp_points" => 0
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Erreur lors du désabonnement."]);
        }

        $stmt->close();
    }

    $check_stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Données manquantes."]);
}
?>