<?php

include 'db.php';

try {
    // Récupérer les données du formulaire
    $surname = $_POST['surname'];
    $name = $_POST['name'];
    $birth = $_POST['birth'];
    $cityData = json_decode($_POST['city-data'], true);
    $city = $cityData['name'];
    $coordinates = $cityData['coordinates'];
    $range = $cityData['range'];
    $mail = $_POST['email'];
    $password = sha1($_POST['password']); // Hachage du mot de passe avec SHA-1
    $profile_picture = ''; // Vous pouvez ajouter une logique pour gérer l'upload de l'image de profil

    // Convertir les données de la ville en JSON pour l'enregistrer dans user_adress
    $user_adress = json_encode($cityData);

    // Vérifier si l'adresse e-mail existe déjà
    $sql = "SELECT COUNT(*) FROM user WHERE user_mail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $mail);
    $stmt->execute();
    $stmt->bind_result($emailExists);
    $stmt->fetch();
    $stmt->close();

    if ($emailExists) {
        echo "Erreur: L'adresse e-mail est déjà utilisée.";
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
        $stmt->bind_param('sssssss', $name, $surname, $user_adress, $mail, $password, $birth, $profile_picture);
        $stmt->execute();
        $stmt->close();

        echo "Nouvel enregistrement créé avec succès";
    }
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
