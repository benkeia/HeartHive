<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer les messages reçus avec informations sur l'expéditeur
$query = "SELECT 
            m.message_id, 
            m.sender_id, 
            m.receiver_id, 
            m.message_content, 
            m.created_at,
            m.is_read,
            u.user_firstname, 
            u.user_name, 
            u.user_profile_picture
          FROM messages m
          JOIN user u ON u.user_id = m.sender_id
          WHERE m.receiver_id = ?
          ORDER BY m.created_at DESC
          LIMIT 10";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
$unread_count = 0;

while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'message_id' => $row['message_id'],
        'sender_id' => $row['sender_id'],
        'message_content' => $row['message_content'],
        'created_at' => $row['created_at'],
        'is_read' => $row['is_read'],
        'user_firstname' => $row['user_firstname'],
        'user_name' => $row['user_name'],
        'user_profile_picture' => $row['user_profile_picture'] ?: 'assets/img/default-avatar.png',
        'formatted_time' => date('H:i', strtotime($row['created_at']))
    ];

    if ($row['is_read'] == 0) {
        $unread_count++;
    }
}

// Compter le nombre total de messages non lus
$count_query = "SELECT COUNT(*) as total FROM messages WHERE receiver_id = ? AND is_read = 0";
$count_stmt = $conn->prepare($count_query);
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$unread_total = $count_result->fetch_assoc()['total'];

echo json_encode([
    'success' => true,
    'messages' => $messages,
    'unread_count' => $unread_total,
    'has_messages' => count($messages) > 0
]);
