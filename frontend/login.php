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

if (!isset($_POST['mail'], $_POST['password'])) {
    sendResponse(false, 'Veuillez remplir tous les champs');
}

$mail = $_POST['mail'];
$password = $_POST['password'];

// Préparation de la requête pour les utilisateurs normaux
$sql = $conn->prepare("SELECT user_password, user_type, user_firstname, user_name, user_id, user_profile_picture, user_adress, user_bio, user_tags FROM user WHERE user_mail = ?");
$sql->bind_param("s", $mail);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (sha1($password) === $row['user_password']) {
        // Définir les variables de session
        $_SESSION['firstname'] = $row['user_firstname'];
        $_SESSION['name'] = $row['user_name'];
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['type'] = $row['user_type'];
        $_SESSION['authentification'] = true;
        $_SESSION['user_profile_picture'] = $row['user_profile_picture'];
        $_SESSION['user_adress'] = $row['user_adress'];
        $_SESSION['user_bio'] = $row['user_bio'];
        $_SESSION['user_tags'] = $row['user_tags'] ?? '{}';

        sendResponse(true, 'Connexion réussie', 'profile.php');
    } else {
        sendResponse(false, 'Mot de passe incorrect');
    }
} else {
    // Vérification pour les associations
    $sql = $conn->prepare("SELECT association_id, association_name, association_mail, association_password, association_desc, association_profile_picture FROM association WHERE association_mail = ?");
    $sql->bind_param("s", $mail);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (sha1($password) === $row['association_password']) {
            // Définir les variables de session pour l'association
            $_SESSION['firstname'] = $row['association_name'];
            $_SESSION['name'] = $row['association_name'];
            $_SESSION['user_id'] = $row['association_id'];
            $_SESSION['type'] = 1; // Indiquer que c'est une association
            $_SESSION['authentification'] = true;
            $_SESSION['user_profile_picture'] = $row['association_profile_picture'] ?? '';
            $_SESSION['user_adress'] = ''; // Pas de colonne association_address
            $_SESSION['user_bio'] = $row['association_desc'] ?? '';

            sendResponse(true, 'Connexion réussie', 'Profile.php');
        } else {
            sendResponse(false, 'Mot de passe incorrect pour l\'association');
        }
    } else {
        sendResponse(false, 'Adresse email non trouvée');
    }
}

$sql->close();
$conn->close();

header('Location: loginPage.php');
