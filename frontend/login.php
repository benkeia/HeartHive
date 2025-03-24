<?php
session_start();
include '../backend/db.php';



header('Content-Type: application/json');

function sendResponse($success, $message, $redirect = null)
{
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'redirect' => $redirect
    ]);
    exit();
}

if (!isset($_POST['mail'], $_POST['password']) || empty($_POST['mail']) || empty($_POST['password'])) {
    sendResponse(false, 'Veuillez remplir tous les champs');
}

$mail = filter_var($_POST['mail'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];

try {
    // Vérification dans la table user
    $sql = $conn->prepare("SELECT * FROM user WHERE user_mail = ?");
    $sql->bind_param("s", $mail);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (sha1($password) === $row['user_password']) {
            $_SESSION['firstname'] = $row['user_firstname'];
            $_SESSION['name'] = $row['user_name'];
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['type'] = $row['user_type'];
            $_SESSION['mail'] = $row['user_mail'];
            $_SESSION['authentification'] = true;
            $_SESSION['user_profile_picture'] = $row['user_profile_picture'] ?? 'assets/uploads/profile_pictures/default.webp';
            $_SESSION['user_adress'] = $row['user_adress'] ?? '';
            $_SESSION['user_bio'] = $row['user_bio'] ?? '';
            $_SESSION['user_tags'] = $row['user_tags'] ?? '{}';

            sendResponse(true, 'Connexion réussie', 'index.php');
        }
        sendResponse(false, 'Mot de passe incorrect');
    }

    // Si pas trouvé dans users, on cherche dans associations
    $sql = $conn->prepare("SELECT * FROM association WHERE association_mail = ?");
    $sql->bind_param("s", $mail);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (sha1($password) === $row['association_password']) {
            $_SESSION['firstname'] = $row['association_name'];
            $_SESSION['name'] = $row['association_name'];
            $_SESSION['user_id'] = $row['association_id'];
            $_SESSION['type'] = 1;
            $_SESSION['authentification'] = true;
            $_SESSION['user_profile_picture'] = 'assets/uploads/profile_pictures/default.webp';
            $_SESSION['user_bio'] = $row['association_desc'] ?? '';
            $_SESSION['user_tags'] = '{}';

            sendResponse(true, 'Connexion réussie', 'profile_asso.php');
        }
        sendResponse(false, 'Mot de passe incorrect');
    }

    sendResponse(false, 'Adresse email non trouvée');
} catch (Exception $e) {
    sendResponse(false, 'Une erreur est survenue');
} finally {
    if (isset($sql)) $sql->close();
    if (isset($conn)) $conn->close();
}
