<?php
try {
    // Connexion à la base de données
    $dsn = 'mysql:host=localhost;dbname=db_HeartHive;charset=utf8';
    $username = 'root';
    $password = 'root';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $username, $password, $options);

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
    $sql = "SELECT COUNT(*) FROM user WHERE user_mail = :mail";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':mail' => $mail]);
    $emailExists = $stmt->fetchColumn();

    if ($emailExists) {
        echo "Erreur: L'adresse e-mail est déjà utilisée.";
    } else {
        // Gérer l'upload de la photo de profil
        if (isset($_FILES['profile-pic']) && $_FILES['profile-pic']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = '../frontend/assets/uploads/profile_pictures/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $profile_picture = $upload_dir . basename($_FILES['profile-pic']['name']);
            move_uploaded_file($_FILES['profile-pic']['tmp_name'], $profile_picture);

            // Enregistrer le chemin relatif dans la base de données
            $profile_picture = 'assets/uploads/profile_pictures/' . basename($_FILES['profile-pic']['name']);
        }

        // Préparer et exécuter la requête d'insertion
        $sql = "INSERT INTO user (user_name, user_surname, user_city, user_range, user_mail, user_password, user_birth_date, user_profile_picture) 
                VALUES (:name, :surname, :city, :range, :mail, :password, :birth, :profile_picture)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':surname' => $surname,
            ':city' => $city,
            ':range' => $range,
            ':mail' => $mail,
            ':password' => $password,
            ':birth' => $birth,
            ':profile_picture' => $profile_picture,
        ]);

        echo "Nouvel enregistrement créé avec succès";
    }
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}
