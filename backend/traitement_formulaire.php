<?php

include 'db.php';
session_start();

try {
    // Récupérer et nettoyer les données du formulaire
    $name = trim(htmlspecialchars($_POST['name']));
    $firstname = trim(htmlspecialchars($_POST['surname']));
    $birth = $_POST['birth'];
    $mail = trim(htmlspecialchars($_POST['email']));
    $password = $_POST['password'];
    $profile_picture = 'assets/uploads/profile_pictures/default.webp'; // Chemin par défaut pour la photo de profil
    $user_type = 0; // Définir le type d'utilisateur par défaut

    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Adresse e-mail invalide.");
    }

    if (strlen($password) < 8) {
        throw new Exception("Le mot de passe doit contenir au moins 8 caractères.");
    }

    // Hachage du mot de passe avec SHA-1
    $hashed_password = sha1($password);

    // Vérifier si l'adresse e-mail existe déjà
    $sql = "SELECT COUNT(*) FROM user WHERE user_mail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $mail);
    $stmt->execute();
    $stmt->bind_result($emailExists);
    $stmt->fetch();
    $stmt->close();

    if ($emailExists) {
        throw new Exception("L'adresse e-mail est déjà utilisée.");
    } else {
        // Gérer l'upload de la photo de profil
        if (isset($_FILES['profile-pic']) && $_FILES['profile-pic']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = '../frontend/assets/uploads/profile_pictures/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            // Générer un nom de fichier unique
            $unique_name = time();
            $profile_picture = $upload_dir . $unique_name;
            move_uploaded_file($_FILES['profile-pic']['tmp_name'], $profile_picture);

            // Enregistrer le chemin relatif dans la base de données
            $profile_picture = 'assets/uploads/profile_pictures/' . $unique_name;
        }
        // Traiter les données de localisation
        $city_data = isset($_POST['city-data']) ? $_POST['city-data'] : '{"name":"","coordinates":[0,0],"range":0}';

        // Valider le format JSON
        if (!empty($city_data)) {
            $decoded = json_decode($city_data, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Si le JSON est invalide, utiliser une valeur par défaut
                $city_data = '{"name":"","coordinates":[0,0],"range":0}';
            }
        }

        // Stocker les données de localisation dans la table user
        $user_address = $city_data;
        // Préparer et exécuter la requête d'insertion
        $sql = "INSERT INTO user (user_name, user_firstname, user_date, user_mail, user_password, user_type, user_profile_picture, user_adress) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssssss', $name, $firstname, $birth, $mail, $hashed_password, $user_type, $profile_picture, $user_address);
        $stmt->execute();
        $stmt->close();

        // Démarrer une session et rediriger vers index.php
        $_SESSION['user_mail'] = $mail;

        header("Location: ../frontend/onboarding.php");
        exit();
    }
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
