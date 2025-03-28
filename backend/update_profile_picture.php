<?php
// Pour le débogage, activez temporairement l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    if (strpos($img, 'data:image/jpeg;base64,') !== false) {
        $img = str_replace('data:image/jpeg;base64,', '', $img);
        $extension = 'jpg';
    } elseif (strpos($img, 'data:image/png;base64,') !== false) {
        $img = str_replace('data:image/png;base64,', '', $img);
        $extension = 'png';
    } else {
        // Format d'image non supporté
        echo json_encode(['status' => 'error', 'message' => 'Format d\'image non supporté']);
        exit;
    }
    
    $img = str_replace(' ', '+', $img);
    
    // Décoder les données
    $imgData = base64_decode($img);
    if ($imgData === false) {
        echo json_encode(['status' => 'error', 'message' => 'Décodage de l\'image échoué']);
        exit;
    }
    
    // Générer un nom de fichier unique
    $filename = 'user_' . $user_id . '_' . time() . '.' . $extension;
    
    // Définir les chemins absolus et relatifs
    $upload_rel_dir = 'assets/uploads/profile_pictures/';
    $root_path = $_SERVER['DOCUMENT_ROOT'] . '/HeartHive/HeartHive/frontend/';
    $upload_abs_dir = $root_path . $upload_rel_dir;
    
    // Créer le dossier s'il n'existe pas
    if (!file_exists($upload_abs_dir)) {
        if (!mkdir($upload_abs_dir, 0777, true)) {
            echo json_encode(['status' => 'error', 'message' => 'Impossible de créer le répertoire ' . $upload_abs_dir]);
            exit;
        }
    }
    
    $file_path = $upload_abs_dir . $filename;
    
    // Supprimer l'ancienne image s'il en existe une
    $stmt = $conn->prepare("SELECT user_profile_picture FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $old_image_path = $row['user_profile_picture'];
        
        if (!empty($old_image_path) && $old_image_path != 'assets/img/default-profile.jpg') {
            $old_image_file = $root_path . $old_image_path;
            
            if (file_exists($old_image_file)) {
                unlink($old_image_file);
            }
        }
    }
    $stmt->close();
    
    // Enregistrer l'image recadrée
    if (file_put_contents($file_path, $imgData)) {
        // Chemin relatif pour la base de données
        $db_path = $upload_rel_dir . $filename;
        
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
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'enregistrement de l\'image. Chemin: ' . $file_path]);
    }
} 
// Garder le code existant pour le téléchargement direct via input type="file"
else if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    // Définir les chemins comme ci-dessus
    $upload_rel_dir = 'assets/uploads/profile_pictures/';
    $root_path = $_SERVER['DOCUMENT_ROOT'] . '/HeartHive/HeartHive/frontend/';
    $upload_abs_dir = $root_path . $upload_rel_dir;
    
    // Créer le dossier s'il n'existe pas
    if (!file_exists($upload_abs_dir)) {
        if (!mkdir($upload_abs_dir, 0777, true)) {
            echo json_encode(['status' => 'error', 'message' => 'Impossible de créer le répertoire']);
            exit;
        }
    }
    
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['profile_picture']['name'];
    $filetype = pathinfo($filename, PATHINFO_EXTENSION);
    
    if (in_array(strtolower($filetype), $allowed)) {
        // Générer un nom de fichier unique
        $new_filename = 'user_' . $user_id . '_' . time() . '.' . $filetype;
        $file_path = $upload_abs_dir . $new_filename;
        
        // Supprimer l'ancienne image (comme ci-dessus)
        $stmt = $conn->prepare("SELECT user_profile_picture FROM user WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $old_image_path = $row['user_profile_picture'];
            
            if (!empty($old_image_path) && $old_image_path != 'assets/img/default-profile.jpg') {
                $old_image_file = $root_path . $old_image_path;
                
                if (file_exists($old_image_file)) {
                    unlink($old_image_file);
                }
            }
        }
        $stmt->close();
        
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $file_path)) {
            // Chemin relatif pour la base de données
            $db_path = $upload_rel_dir . $new_filename;
            
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
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors du déplacement du fichier']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Type de fichier non autorisé']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Aucune image fournie']);
}
?>