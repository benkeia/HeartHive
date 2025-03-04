<?php

include 'db.php';
session_start();

try {
    // Récupérer et nettoyer les données du formulaire
    $surname = trim(htmlspecialchars($_POST['surname']));
    $name = trim(htmlspecialchars($_POST['name']));
    $birth = $_POST['birth'];
    $cityData = json_decode($_POST['city-data'], true);
    $city = htmlspecialchars($cityData['name']);
    $coordinates = htmlspecialchars($cityData['coordinates']);
    $range = htmlspecialchars($cityData['range']);
    $mail = trim(htmlspecialchars($_POST['email']));
    $password = $_POST['password'];
    $profile_picture = 'assets/uploads/profile_pictures/default.webp'; // Chemin par défaut pour la photo de profil



    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Adresse e-mail invalide.");
    }

    if (strlen($password) < 8) {
        throw new Exception("Le mot de passe doit contenir au moins 8 caractères.");
    }

    // Convertir les données de la ville en JSON pour l'enregistrer dans user_adress
    $user_adress = json_encode($cityData);

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

        // Préparer et exécuter la requête d'insertion
        $sql = "INSERT INTO user (user_name, user_firstname, user_adress, user_mail, user_password, user_date, user_profile_picture) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssssss', $name, $surname, $user_adress, $mail, $hashed_password, $birth, $profile_picture);
        $stmt->execute();
        $stmt->close();

        // Démarrer une session et rediriger vers index.php
        $_SESSION['user_mail'] = $mail;

        header("Location: ../frontend/index.php");
        exit();
    }
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
