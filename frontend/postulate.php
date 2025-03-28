<?php
session_start();
include '../backend/db.php';

header('Content-Type: application/json');

if (isset($_POST['association_id']) && isset($_SESSION['user_id'])) {
    $association_id = $_POST['association_id'];
    $user_id = $_SESSION['user_id'];
    $date = date('Y-m-d H:i:s');

    // Vérifier si la postulation existe déjà
    $check_query = "SELECT * FROM postulation WHERE postulation_association_id_fk = ? AND postulation_user_id_fk = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $association_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Vous avez déjà postulé. ❌"]);
    } else {
        $query = "INSERT INTO postulation (postulation_association_id_fk, postulation_user_id_fk, postulation_date) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $association_id, $user_id, $date);

        if ($stmt->execute()) {

            $xp_added = false;
            $xp_points = 25; // Valeur pour apply_association
            $action = 'apply_association';
            $details = 'association_' . $association_id;
            
            try {
                // Vérifier si déjà attribué pour cette association
                $check = $conn->prepare("SELECT * FROM experience_transactions WHERE user_id = ? AND reason = ? AND related_entity_type = ?");
                $check->bind_param("iss", $user_id, $action, $details);
                $check->execute();
                $result = $check->get_result();
                
                if ($result->num_rows == 0) { // Aucun enregistrement trouvé, c'est OK
                    // Ajouter la transaction d'XP
                    $stmt_xp = $conn->prepare("INSERT INTO experience_transactions (user_id, points, reason, related_entity_type) VALUES (?, ?, ?, ?)");
                    $stmt_xp->bind_param("iiss", $user_id, $xp_points, $action, $details);
                    $stmt_xp->execute();
                    
                    // Mettre à jour ou créer l'entrée d'XP de l'utilisateur
                    $stmt_total = $conn->prepare("SELECT * FROM user_experience WHERE user_id = ?");
                    $stmt_total->bind_param("i", $user_id);
                    $stmt_total->execute();
                    $result_total = $stmt_total->get_result();
                    
                    if ($result_total->num_rows > 0) {
                        // Mettre à jour l'XP existante
                        $user_exp = $result_total->fetch_assoc();
                        $new_total = $user_exp['total_points'] + $xp_points;
                        
                        $stmt_update = $conn->prepare("UPDATE user_experience SET total_points = ? WHERE user_id = ?");
                        $stmt_update->bind_param("ii", $new_total, $user_id);
                        $stmt_update->execute();
                    } else {
                        // Créer une nouvelle entrée
                        $stmt_insert = $conn->prepare("INSERT INTO user_experience (user_id, total_points, current_level, points_to_next_level) VALUES (?, ?, 1, 100)");
                        $stmt_insert->bind_param("ii", $user_id, $xp_points);
                        $stmt_insert->execute();
                        $new_total = $xp_points;
                    }
                    
                    $xp_added = true;
                }
            } catch (Exception $e) {
                // En cas d'erreur, continuer sans XP
                file_put_contents('xp_error.log', date('Y-m-d H:i:s') . " - Erreur: " . $e->getMessage() . "\n", FILE_APPEND);
            }
            
            // Envoyer la réponse avec les informations d'XP
            echo json_encode([
                "status" => "success", 
                "message" => "Vous suivez maintenant cette association ✅",
                "xp_added" => $xp_added,
                "xp_points" => $xp_added ? $xp_points : 0
            ]);
            
        } else {
            echo json_encode(["status" => "error", "message" => "Erreur lors du suivi de l'association. ❌"]);
        }

        $stmt->close();
    }

    $check_stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Données manquantes. ❌"]);
}
?>