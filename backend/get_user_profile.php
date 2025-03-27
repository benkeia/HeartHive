<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

// Vérifier que l'ID est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID utilisateur invalide']);
    exit;
}

$user_id = intval($_GET['id']);

// Récupérer les informations de l'utilisateur
$stmt = $conn->prepare("
    SELECT 
        user_id as id,
        user_firstname as first_name, 
        user_name as last_name, 
        user_mail as email,
        user_profile_picture as profile_picture,
        user_bio as bio
    FROM user 
    WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
    exit;
}

$user = $result->fetch_assoc();

// Récupérer les compétences et intérêts de l'utilisateur
$user_tags = json_decode($user['user_tags'] ?? '{}', true);

// Construire la réponse
$response = [
    'success' => true,
    'user' => [
        'id' => $user['id'],
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'email' => $user['email'],
        'profile_picture' => $user['profile_picture'],
        'bio' => $user['bio'],
        'skills' => $user_tags['skills'] ?? [],
        'interests' => $user_tags['interests'] ?? []
    ]
];

echo json_encode($response);
