<?php
// filepath: /Applications/MAMP/htdocs/HeartHive/backend/save_onboarding.php
session_start();
require_once 'db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Vérifier si l'email est dans la session (au cas où l'utilisateur vient de s'inscrire)
    if (isset($_SESSION['user_mail'])) {
        // Récupérer l'ID utilisateur depuis l'email
        $stmt = $conn->prepare("SELECT user_id FROM user WHERE user_mail = ?");
        $stmt->bind_param("s", $_SESSION['user_mail']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $_SESSION['user_id'] = $row['user_id'];
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
            exit;
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
        exit;
    }
}

// Obtenir les données
$response = ['success' => false, 'message' => 'Aucune donnée reçue'];

// Traiter les données JSON pour bénévole
if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data && isset($data['user_type']) && $data['user_type'] === 0) {
        // Mise à jour du type utilisateur dans la base de données
        $stmt = $conn->prepare("UPDATE user SET user_type = ? WHERE user_id = ?");
        $stmt->bind_param("ii", $data['user_type'], $_SESSION['user_id']);

        if ($stmt->execute()) {
            // Mettre à jour les tags d'intérêts et compétences
            if (isset($data['interests']) && is_array($data['interests']) && isset($data['skills']) && is_array($data['skills'])) {
                $userTags = json_encode([
                    'interests' => $data['interests'],
                    'skills' => $data['skills']
                ]);

                $stmt = $conn->prepare("UPDATE user SET user_tags = ? WHERE user_id = ?");
                $stmt->bind_param("si", $userTags, $_SESSION['user_id']);
                $stmt->execute();
            }

            // Mettre à jour les disponibilités
            if (isset($data['availability']) && is_array($data['availability'])) {
                $availabilityData = json_encode($data['availability']);
                $stmt = $conn->prepare("UPDATE user SET user_disponibility = ? WHERE user_id = ?");
                $stmt->bind_param("si", $availabilityData, $_SESSION['user_id']);
                $stmt->execute();
            }

            // Récupérer les informations complètes de l'utilisateur pour la session
            $userQuery = $conn->prepare("SELECT * FROM user WHERE user_id = ?");
            $userQuery->bind_param("i", $_SESSION['user_id']);
            $userQuery->execute();
            $userData = $userQuery->get_result()->fetch_assoc();

            if ($userData) {
                // Mettre à jour toutes les variables de session
                $_SESSION['firstname'] = $userData['user_firstname'];
                $_SESSION['name'] = $userData['user_name'];
                $_SESSION['type'] = $userData['user_type'];
                $_SESSION['mail'] = $userData['user_mail'];
                $_SESSION['authentification'] = true;
                $_SESSION['user_profile_picture'] = $userData['user_profile_picture'] ?? 'assets/uploads/profile_pictures/default.webp';
                $_SESSION['user_adress'] = $userData['user_adress'] ?? '';
                $_SESSION['user_bio'] = $userData['user_bio'] ?? '';
                $_SESSION['user_tags'] = $userTags ?? '{}'; // Utiliser les tags qu'on vient de mettre à jour
                $_SESSION['onboarding_completed'] = true;
            }

            $response = ['success' => true, 'message' => 'Profil bénévole mis à jour avec succès'];
        } else {
            $response = ['success' => false, 'message' => 'Erreur lors de la mise à jour du type utilisateur: ' . $conn->error];
        }
    }
}
// Traiter les données du formulaire pour association
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_type']) && $_POST['user_type'] == 1) {
        // Vérifier si l'association existe déjà
        $stmt = $conn->prepare("SELECT * FROM association WHERE association_name = ? OR association_siren = ?");
        $stmt->bind_param("ss", $_POST['association_name'], $_POST['association_siren']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $response = ['success' => false, 'message' => 'Une association avec ce nom ou ce numéro SIREN/SIRET existe déjà'];
        } else {
            // Préparer les données pour l'insertion
            $location = $_POST['association_location'] ?? '{}';

            // Traitement des images
            $profile_picture = 'assets/uploads/profile_pictures/default.webp';
            $background_image = 'assets/uploads/background_image/default.jpg';

            // Traiter l'image de profil si fournie
            if (isset($_POST['cropped_profile_data']) && !empty($_POST['cropped_profile_data'])) {
                $img = $_POST['cropped_profile_data'];
                $img = str_replace('data:image/jpeg;base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                $data = base64_decode($img);
                $profile_filename = 'association_' . time() . '_profile.jpg';
                $profile_path = '../frontend/assets/uploads/profile_pictures/' . $profile_filename;
                file_put_contents($profile_path, $data);
                $profile_picture = 'assets/uploads/profile_pictures/' . $profile_filename;
            }

            // Traiter l'image de fond si fournie
            if (isset($_POST['cropped_background_data']) && !empty($_POST['cropped_background_data'])) {
                $img = $_POST['cropped_background_data'];
                $img = str_replace('data:image/jpeg;base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                $data = base64_decode($img);
                $bg_filename = 'association_' . time() . '_bg.jpg';
                $bg_path = '../frontend/assets/uploads/background_image/' . $bg_filename;
                file_put_contents($bg_path, $data);
                $background_image = 'assets/uploads/background_image/' . $bg_filename;
            }

            // Insérer l'association dans la base de données avec tous les champs requis
            $stmt = $conn->prepare("INSERT INTO association (
                association_name, 
                association_siren, 
                association_date, 
                association_desc,
                association_category, 
                association_mail,
                association_address,
                association_postal,
                association_city,
                association_country,
                association_location,
                association_phone,
                association_website,
                user_id,
                association_profile_picture,
                association_background_image
            ) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param(
                "ssssssssssssiss",
                $_POST['association_name'],
                $_POST['association_siren'],
                $_POST['association_desc'],
                $_POST['association_category'],
                $_POST['association_mail'],
                $_POST['association_address'],
                $_POST['association_postal'],
                $_POST['association_city'],
                $_POST['association_country'],
                $location,
                $_POST['association_phone'],
                $_POST['association_website'],
                $_SESSION['user_id'],
                $profile_picture,
                $background_image
            );

            if ($stmt->execute()) {
                $association_id = $conn->insert_id;

                // Mettre à jour le type utilisateur
                $stmt = $conn->prepare("UPDATE user SET user_type = 1 WHERE user_id = ?");
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();

                // Récupérer les informations complètes de l'utilisateur pour la session
                $userQuery = $conn->prepare("SELECT * FROM user WHERE user_id = ?");
                $userQuery->bind_param("i", $_SESSION['user_id']);
                $userQuery->execute();
                $userData = $userQuery->get_result()->fetch_assoc();

                if ($userData) {
                    // Mettre à jour toutes les variables de session
                    $_SESSION['firstname'] = $userData['user_firstname'];
                    $_SESSION['name'] = $userData['user_name'];
                    $_SESSION['user_id'] = $userData['user_id'];
                    $_SESSION['type'] = 1; // Type association
                    $_SESSION['mail'] = $userData['user_mail'];
                    $_SESSION['authentification'] = true;
                    $_SESSION['user_profile_picture'] = $userData['user_profile_picture'] ?? 'assets/uploads/profile_pictures/default.webp';
                    $_SESSION['user_adress'] = $userData['user_adress'] ?? '';
                    $_SESSION['user_bio'] = $_POST['association_desc'] ?? '';
                    $_SESSION['user_tags'] = $userData['user_tags'] ?? '{}';
                    $_SESSION['association_id'] = $association_id;
                    $_SESSION['onboarding_completed'] = true;
                }

                $response = ['success' => true, 'message' => 'Association créée avec succès'];
            } else {
                $response = ['success' => false, 'message' => 'Erreur lors de la création de l\'association: ' . $conn->error];
            }
        }
    }
}

// Envoyer la réponse JSON
header('Content-Type: application/json');
echo json_encode($response);
