<?php
session_start();
include('db.php');

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour envoyer un message']);
    exit;
}

// Vérifier si les données requises sont présentes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_id']) && isset($_POST['message'])) {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = intval($_POST['receiver_id']);
    $message = trim($_POST['message']);

    // Vérifier que le message n'est pas vide
    if (empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Le message ne peut pas être vide']);
        exit;
    }

    // Insérer le message dans la base de données
    $query = "INSERT INTO messages (sender_id, receiver_id, message_content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);

    if ($stmt->execute()) {
        // Récupérer l'ID et la date du message créé
        $message_id = $stmt->insert_id;
        $date_query = "SELECT created_at FROM messages WHERE message_id = ?";
        $date_stmt = $conn->prepare($date_query);
        $date_stmt->bind_param("i", $message_id);
        $date_stmt->execute();
        $created_at = $date_stmt->get_result()->fetch_assoc()['created_at'];

        echo json_encode([
            'success' => true,
            'message' => 'Message envoyé avec succès',
            'data' => [
                'message_id' => $message_id,
                'created_at' => $created_at,
                'formatted_time' => date('H:i', strtotime($created_at))
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'envoi du message']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
}
