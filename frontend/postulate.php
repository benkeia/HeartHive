<?php
session_start();
include '../backend/db.php';

header('Content-Type: application/json'); // Indique qu'on renvoie du JSON

if (isset($_POST['association_id']) && isset($_SESSION['user_id'])) {
    $association_id = $_POST['association_id'];
    $user_id = $_SESSION['user_id'];
    $date = date('Y-m-d H:i:s');

    // Vérifier si la postulation existe déjà
    $check_query = "SELECT * FROM postulation WHERE postulation_association_id_fk = ? AND postulation_user_id_fk = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $association_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Vous avez déjà postulé. ❌"]);
    } else {
        // Insérer la nouvelle postulation
        $query = "INSERT INTO postulation (postulation_association_id_fk, postulation_user_id_fk, postulation_date) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $association_id, $user_id, $date);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Inscription validée ✅"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Erreur lors de l'inscription. ❌"]);
        }

        $stmt->close();
    }

    $check_stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Données manquantes. ❌"]);
}
?>
