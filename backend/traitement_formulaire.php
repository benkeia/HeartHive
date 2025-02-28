<?php

include 'db.php';

try {
    // Récupérer les données du formulaire
    $surname = $_POST['surname'];
    $name = $_POST['name'];
    $birth = $_POST['birth'];
    $city = $_POST['city'];
    $range = isset($_POST['range']) ? (int)$_POST['range'] : 0; // Assurez-vous que range est un entier
    $mail = $_POST['email'];
    $password = sha1($_POST['password']); // Hachage du mot de passe avec SHA-1
    $profile_picture = ''; // Vous pouvez ajouter une logique pour gérer l'upload de l'image de profil

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
        $sql = "INSERT INTO user (user_name, user_firstname, user_adress, user_type, user_mail, user_password, user_date, user_profile_picture) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssissss', $name, $surname, $city, $range, $mail, $password, $birth, $profile_picture);
        $stmt->execute();
        $stmt->close();

        echo "Nouvel enregistrement créé avec succès";
    }
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
