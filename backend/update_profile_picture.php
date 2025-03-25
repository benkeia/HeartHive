<?php
// Désactiver l'affichage des erreurs PHP
error_reporting(0);
ini_set('display_errors', 0);

// Spécifier que la réponse est du JSON
header('Content-Type: application/json');

session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Traiter l'image recadrée si elle existe
if (isset($_POST['cropped_image']) && !empty($_POST['cropped_image'])) {
    // Récupérer les données de l'image
    $img = $_POST['cropped_image'];
    
    // Extraire les données binaires de l'URL base64
    $img = str_replace('data:image/jpeg;base64,', '', $img);
    $img = str_replace('data:image/png;base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    
    // Décoder les données
    $imgData = base64_decode($img);
    
    // Générer un nom de fichier unique
    $filename = uniqid() . '.jpg';
    
    // Chemin pour enregistrer l'image
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/HeartHive/HeartHive/uploads/profile_pictures/';
    
    // Créer le dossier s'il n'existe pas
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file = $upload_dir . $filename;
    
    // Supprimer l'ancienne image s'il en existe une
    $stmt = $conn->prepare("SELECT user_profile_picture FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $old_image_path = $row['user_profile_picture'];
        
        if (!empty($old_image_path) && $old_image_path != 'assets/img/default-profile.jpg') {
            $old_image_file = $_SERVER['DOCUMENT_ROOT'] . '/HeartHive/HeartHive/' . str_replace('../', '', $old_image_path);
            
            if (file_exists($old_image_file)) {
                unlink($old_image_file);
            }
        }
    }
    $stmt->close();
    
    // Enregistrer l'image recadrée
    if (file_put_contents($file, $imgData)) {
        // Mise à jour de la base de données
        $db_path = '../uploads/profile_pictures/' . $filename;
        
        $stmt = $conn->prepare("UPDATE user SET user_profile_picture = ? WHERE user_id = ?");
        $stmt->bind_param("si", $db_path, $user_id);
        
        if ($stmt->execute()) {
            // Mettre à jour la session
            $_SESSION['user_profile_picture'] = $db_path;
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Photo de profil mise à jour avec succès',
                'image_path' => $db_path
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur de mise à jour de la base de données: ' . $conn->error]);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'enregistrement de l\'image']);
    }
} 
// Garder le code existant pour le téléchargement direct via input type="file"
else if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    // Votre code existant pour le traitement de l'upload classique
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['profile_picture']['name'];
    $filetype = pathinfo($filename, PATHINFO_EXTENSION);
    
    // Le reste du code existant...
} else {
    echo json_encode(['status' => 'error', 'message' => 'Aucune image fournie']);
}
?>