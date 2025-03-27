<?php
session_start();
include 'db.php';

// Toujours commencer par l'en-tête pour éviter les erreurs de format
header('Content-Type: application/json');

// Structure try-catch pour éviter les erreurs PHP qui casseraient le JSON
try {
    // Vérifier que l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Non authentifié']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $last_check = isset($_GET['last_check']) ? intval($_GET['last_check']) : 0;

    // Requête pour récupérer les nouveaux messages
    $query = "SELECT 
                u.user_id,
                u.user_firstname,
                u.user_name,
                u.user_profile_picture,
                m.message_content as last_message,
                m.created_at,
                COUNT(m2.message_id) as unread_count
              FROM user u
              JOIN messages m ON m.sender_id = u.user_id
              LEFT JOIN messages m2 ON m2.sender_id = u.user_id AND m2.receiver_id = ? AND m2.is_read = 0
              WHERE m.receiver_id = ?
                AND m.created_at > FROM_UNIXTIME(?)
                AND m.message_id = (
                    SELECT MAX(message_id) 
                    FROM messages 
                    WHERE sender_id = u.user_id AND receiver_id = ?
                )
              GROUP BY u.user_id, u.user_firstname, u.user_name, u.user_profile_picture, m.message_content, m.created_at
              ORDER BY m.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiii", $user_id, $user_id, $last_check, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $conversations = [];
    while ($row = $result->fetch_assoc()) {
        $conversations[] = [
            'user_id' => $row['user_id'],
            'user_firstname' => $row['user_firstname'],
            'user_name' => $row['user_name'],
            'user_profile_picture' => $row['user_profile_picture'] ?: 'assets/img/default-avatar.png',
            'last_message' => $row['last_message'],
            'created_at' => $row['created_at'],
            'formatted_time' => date('H:i', strtotime($row['created_at'])),
            'unread_count' => $row['unread_count']
        ];
    }

    echo json_encode([
        'success' => true,
        'conversations' => $conversations,
        'current_timestamp' => time()
    ]);
} catch (Exception $e) {
    // En cas d'erreur, renvoyer un JSON valide avec le message d'erreur
    error_log("Erreur dans check_new_conversations.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => "Une erreur s'est produite lors de la vérification des messages",
        'error' => $e->getMessage(),
        'current_timestamp' => time()
    ]);
}
