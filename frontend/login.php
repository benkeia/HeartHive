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

// Préparation de la requête
$sql = $conn->prepare("SELECT user_password, user_type, user_firstname, user_name, user_id FROM user WHERE user_mail = ?");
$sql->bind_param("s", $mail);
$sql->execute();
$result = $sql->get_result(); // Récupération des résultats

// Vérifier si l'utilisateur existe
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc(); // Récupération des données sous forme de tableau associatif

    // Comparaison du mot de passe avec SHA1
    if (sha1($password) === $row['user_password']) {
        $_SESSION['firstname'] = $row['user_firstname'];
        $_SESSION['name'] = $row['user_name'];
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['type'] = $row['user_type'];
        $_SESSION['authentification'] = true;

        // Redirection en fonction du type d'utilisateur
        switch ($row['user_type']) {
            case 0:
                header('Location: association.php');
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
    echo "Utilisateur ou mot de passe incorrect.";
    exit();
}

$sql->close();
$conn->close();

?>
