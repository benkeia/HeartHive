<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['type'] != 1) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Traitement de l'image
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = '../frontend/uploads/missions/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $image_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_url = 'uploads/missions/' . $image_name;
        }
    }

    // Préparation des données JSON
    $skills = !empty($_POST['skills']) ? json_encode(array_map('trim', explode(',', $_POST['skills']))) : '[]';
    $tags = !empty($_POST['tags']) ? json_encode(array_map('trim', explode(',', $_POST['tags']))) : '[]';

    // Préparation des données de localisation
    $location_data = json_decode($_POST['location_data'], true);
    $location = json_encode([
        'name' => $location_data['name'],
        'coordinates' => $location_data['coordinates']
    ]);

    // Insertion dans la base de données
    $query = "INSERT INTO missions (
        association_id,
        title,
        description,
        image_url,
        location,
        volunteers_needed,
        skills_required,
        tags
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);

    // Conversion en entier pour volunteers_needed
    $volunteers = intval($_POST['volunteers_needed']);

    $stmt->bind_param(
        "issssiss",
        $_SESSION['user_id'],    // association_id (i)
        $_POST['title'],         // title (s)
        $_POST['description'],   // description (s)
        $image_url,              // image_url (s)
        $location,               // location (s) - stocké en JSON
        $volunteers,             // volunteers_needed (i)
        $skills,                 // skills_required (s)
        $tags                    // tags (s)
    );

    if ($stmt->execute()) {
        header('Location: ../frontend/index_asso.php?success=1');
    } else {
        header('Location: ../frontend/index_asso.php?error=1&message=' . urlencode($conn->error));
    }
} else {
    header('Location: ../frontend/index_asso.php');
}
