<?php
session_start();
include '../backend/db.php';

// Vérifier si les champs sont remplis
if (!isset($_POST['mail'], $_POST['password'])) {
    header('Location: loginPage.php');
    exit();
}

$mail = $_POST['mail'];
$password = $_POST['password'];

// Préparation de la requête pour les utilisateurs normaux
$sql = $conn->prepare("SELECT user_password, user_type, user_firstname, user_name, user_id, user_profile_picture, user_adress, user_bio, user_tags FROM user WHERE user_mail = ?");
$sql->bind_param("s", $mail);
$sql->execute();
$result = $sql->get_result();

// Vérifier si l'utilisateur existe dans la table user
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Comparaison du mot de passe avec SHA1
    if (sha1($password) === $row['user_password']) {
        $_SESSION['firstname'] = $row['user_firstname'];
        $_SESSION['name'] = $row['user_name'];
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['type'] = $row['user_type'];
        $_SESSION['authentification'] = true;
        $_SESSION['user_profile_picture'] = $row['user_profile_picture'];
        $_SESSION['user_adress'] = $row['user_adress'];
        $_SESSION['user_bio'] = $row['user_bio'];
        $_SESSION['user_tags'] = $row['user_tags'] ?? '{}';

        // Redirection en fonction du type d'utilisateur
        switch ($row['user_type']) {
            case 0:
                header('Location: profile.php');
                exit();
            case 1:
                header('Location: profile.php');
                exit();
            default:
                header('Location: defaultpage.php');
                exit();
        }
    } else {
        echo "Mot de passe incorrect.";
        exit();
    }
} else {
    // Si l'utilisateur n'est pas trouvé dans la table user, chercher dans la table association
    $sql = $conn->prepare("SELECT association_id, association_name, association_mail, association_password, association_desc, association_profile_picture FROM association WHERE association_mail = ?");
    $sql->bind_param("s", $mail);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Comparaison du mot de passe avec SHA1 (ou le hachage utilisé pour les associations)
        if (sha1($password) === $row['association_password']) {
            // Stocker les informations de l'association dans la session
            $_SESSION['firstname'] = $row['association_name'];
            $_SESSION['name'] = $row['association_name'];
            $_SESSION['user_id'] = $row['association_id'];
            $_SESSION['type'] = 1; // Indiquer que c'est une association
            $_SESSION['authentification'] = true;
            $_SESSION['user_profile_picture'] = $row['association_profile_picture'] ?? '';
            $_SESSION['user_adress'] = ''; // Pas de colonne association_address
            $_SESSION['user_bio'] = $row['association_desc'] ?? '';


            // Rediriger vers la page d'association
            header('Location: Profile.php');
            exit();
        } else {
            echo "Mot de passe incorrect pour l'association.";
            exit();
        }
    } else {
        echo "Adresse email non trouvée. Veuillez vérifier vos identifiants.";
        exit();
    }
}

$sql->close();
$conn->close();
