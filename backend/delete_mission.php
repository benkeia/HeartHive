<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['type'] != 1) {
    header('Location: ../login.php');
    exit();
}

if (isset($_GET['id'])) {
    $mission_id = intval($_GET['id']);

    // Vérifier que la mission appartient bien à l'association
    $query = "DELETE FROM missions WHERE mission_id = ? AND association_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $mission_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        header('Location: ../frontend/index_asso.php?success=2');
    } else {
        header('Location: ../frontend/index_asso.php?error=2');
    }
} else {
    header('Location: ../frontend/index_asso.php');
}
