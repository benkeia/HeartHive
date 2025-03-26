<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
    exit;
}

// Récupérer les données
$user_id = $_SESSION['user_id'];
$action = isset($_POST['action']) ? $_POST['action'] : '';
$details = isset($_POST['details']) ? $_POST['details'] : '';

// Définir les points pour chaque action
$xp_values = [
    'complete_profile' => 50,
    'update_profile' => 10,
    'add_profile_picture' => 20,
    'add_skill' => 5,
    'add_interest' => 5,
    'set_availability' => 15,
    'first_login' => 30,
    'daily_login' => 5,

    'apply_association' => 25,     // Candidature à une association
    'accepted_association' => 75,  // Accepté dans une association
    'complete_mission' => 100,     // Mission accomplie
];

// Si l'action n'existe pas dans notre tableau
if (!isset($xp_values[$action])) {
    echo json_encode(['status' => 'error', 'message' => 'Action inconnue']);
    exit;
}

// Points à attribuer
$points = $xp_values[$action];

// Par ce code modifié:
    if ($action != 'update_profile' && $action != 'add_skill' && $action != 'add_interest' && $action != 'daily_login' && $action != 'apply_association') {
        $check = $conn->prepare("SELECT * FROM experience_transactions WHERE user_id = ? AND reason = ?");
        $check->bind_param("is", $user_id, $action);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows > 0) {
            echo json_encode([
                'status' => 'info', 
                'message' => 'Points déjà attribués pour cette action'
            ]);
            exit;
        }
    } 
    // Vérification spéciale pour la candidature à une association
    else if ($action == 'apply_association') {
        $check = $conn->prepare("SELECT * FROM experience_transactions WHERE user_id = ? AND reason = ? AND related_entity_type = ?");
        $check->bind_param("iss", $user_id, $action, $details);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows > 0) {
            echo json_encode([
                'status' => 'info', 
                'message' => 'Points déjà attribués pour cette association'
            ]);
            exit;
        }
    }

// Si c'est une connexion quotidienne, vérifier si c'est déjà fait aujourd'hui
if ($action == 'daily_login') {
    $today = date('Y-m-d');
    $check = $conn->prepare("SELECT * FROM experience_transactions WHERE user_id = ? AND reason = ? AND DATE(transaction_date) = ?");
    $check->bind_param("iss", $user_id, $action, $today);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode([
            'status' => 'info', 
            'message' => 'Points déjà attribués pour aujourd\'hui'
        ]);
        exit;
    }
}

try {
    // Commencer une transaction
    $conn->begin_transaction();
    
    // Ajouter la transaction d'XP
    $stmt = $conn->prepare("INSERT INTO experience_transactions (user_id, points, reason, related_entity_type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $user_id, $points, $action, $details);
    $stmt->execute();
    
    // Mettre à jour ou créer l'entrée d'XP de l'utilisateur
    $stmt = $conn->prepare("SELECT * FROM user_experience WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Mettre à jour l'XP existante
        $user_exp = $result->fetch_assoc();
        $new_total = $user_exp['total_points'] + $points;
        
        $stmt = $conn->prepare("UPDATE user_experience SET total_points = ? WHERE user_id = ?");
        $stmt->bind_param("ii", $new_total, $user_id);
    } else {
        // Créer une nouvelle entrée
        $stmt = $conn->prepare("INSERT INTO user_experience (user_id, total_points, current_level, points_to_next_level) VALUES (?, ?, 1, 100)");
        $stmt->bind_param("ii", $user_id, $points);
        $new_total = $points;
    }
    $stmt->execute();
    
    // Vérifier le niveau
    check_level_up($conn, $user_id, $new_total);
    
    // Valider la transaction
    $conn->commit();
    
    echo json_encode([
        'status' => 'success', 
        'message' => 'Points d\'expérience ajoutés avec succès',
        'points' => $points,
        'total' => $new_total
    ]);
    
} catch (Exception $e) {
    // Annuler la transaction en cas d'erreur
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

/**
 * Vérifie si l'utilisateur monte de niveau
 */
function check_level_up($conn, $user_id, $total_points) {
    // Seuils de niveaux
    $levels = [
        1 => 0,
        2 => 100,
        3 => 250,
        4 => 500,
        5 => 1000,
        6 => 2000,
        7 => 3500,
        8 => 5000,
        9 => 7500,
        10 => 10000
    ];
    
    // Récupérer le niveau actuel de l'utilisateur
    $stmt = $conn->prepare("SELECT current_level FROM user_experience WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_data = $result->fetch_assoc();
    $old_level = $current_data['current_level'];
    
    // Déterminer le nouveau niveau actuel
    $new_level = 1;
    $next_level_points = 100;
    
    foreach ($levels as $level => $threshold) {
        if ($total_points >= $threshold) {
            $new_level = $level;
        } else {
            $next_level_points = $threshold;
            break;
        }
    }
    
    // Mettre à jour le niveau
    $stmt = $conn->prepare("UPDATE user_experience SET current_level = ?, points_to_next_level = ? WHERE user_id = ?");
    $stmt->bind_param("iii", $new_level, $next_level_points, $user_id);
    $stmt->execute();
    
    // Vérifier si l'utilisateur a gagné un niveau
    $level_up = ($new_level > $old_level);
    
    if ($level_up) {
        // Ajouter une récompense pour le niveau
        try {
            $stmt = $conn->prepare("INSERT INTO user_rewards (user_id, reward_type, reward_name, acquired_date) 
                                   VALUES (?, 'level', ?, NOW())");
            
            // Noms des niveaux (à synchroniser avec le frontend)
            $level_names = [
                1 => "Bénévole Débutant",
                2 => "Bénévole Actif",
                3 => "Bénévole Engagé",
                4 => "Bénévole Expérimenté",
                5 => "Bénévole Expert",
                6 => "Bénévole Maître",
                7 => "Bénévole Émérite",
                8 => "Bénévole Légendaire",
                9 => "Bénévole Héroïque",
                10 => "Bénévole Mythique"
            ];
            
            $reward_name = isset($level_names[$new_level]) ? $level_names[$new_level] : "Niveau " . $new_level;
            $stmt->bind_param("is", $user_id, $reward_name);
            $stmt->execute();
        } catch (Exception $e) {
            // Ignorer les erreurs de récompense, juste les logger
            file_put_contents('level_rewards_error.log', date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n", FILE_APPEND);
        }
    }
    
    return [
        'level_up' => $level_up,
        'old_level' => $old_level,
        'new_level' => $new_level
    ];
}

// Puis dans la section principale de votre code, modifiez cette partie:

try {
    // ... reste du code inchangé ...
    
    // Vérifier le niveau
    $level_result = check_level_up($conn, $user_id, $new_total);
    
    // Valider la transaction
    $conn->commit();
    
    // Réponse différente si l'utilisateur a monté de niveau
    if ($level_result['level_up']) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Points d\'expérience ajoutés avec succès',
            'points' => $points,
            'total' => $new_total,
            'level_up' => true,
            'old_level' => $level_result['old_level'],
            'new_level' => $level_result['new_level']
        ]);
    } else {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Points d\'expérience ajoutés avec succès',
            'points' => $points,
            'total' => $new_total
        ]);
    }
} catch (Exception $e) {
    // ... reste du code inchangé ...
    // Mettre à jour le niveau
    $stmt = $conn->prepare("UPDATE user_experience SET current_level = ?, points_to_next_level = ? WHERE user_id = ?");
    $stmt->bind_param("iii", $current_level, $next_level_points, $user_id);
    $stmt->execute();
}
    

?>