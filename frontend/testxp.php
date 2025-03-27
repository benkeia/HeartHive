<?php
session_start();
include '../backend/db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour accéder à cette page.");
}

// Récupérer les données de l'utilisateur
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM user_experience WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $xp_data = $result->fetch_assoc();
    
    echo "<h1>Diagnostic du système XP</h1>";
    echo "<p>Utilisateur ID: " . $user_id . "</p>";
    echo "<p>Points XP actuels: " . $xp_data['total_points'] . "</p>";
    echo "<p>Niveau actuel: " . $xp_data['current_level'] . "</p>";
    echo "<p>Points requis pour le niveau suivant: " . $xp_data['points_to_next_level'] . "</p>";
    
    // Recalculer le niveau basé sur les seuils
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
    
    $calculated_level = 1;
    foreach ($levels as $level => $threshold) {
        if ($xp_data['total_points'] >= $threshold) {
            $calculated_level = $level;
        } else {
            break;
        }
    }
    
    $next_level = $calculated_level + 1;
    $calculated_next_points = isset($levels[$next_level]) ? $levels[$next_level] : 999999;
    
    echo "<p>Niveau calculé: " . $calculated_level . "</p>";
    echo "<p>Points calculés pour le niveau suivant: " . $calculated_next_points . "</p>";
    
    if ($calculated_level != $xp_data['current_level'] || $calculated_next_points != $xp_data['points_to_next_level']) {
        echo "<p style='color: red;'>Incohérence détectée. Le niveau en base de données ne correspond pas au niveau calculé.</p>";
        echo "<p><a href='?fix=1'>Corriger le niveau</a></p>";
    } else {
        echo "<p style='color: green;'>Les données sont cohérentes.</p>";
    }
    
    // Tester l'ajout de points XP
    echo "<h2>Tester l'ajout de points XP</h2>";
    echo "<form method='post'>";
    echo "<select name='action'>";
    echo "<option value='daily_login'>Connexion quotidienne (+5 XP)</option>";
    echo "<option value='update_profile'>Mise à jour du profil (+10 XP)</option>";
    echo "<option value='add_skill'>Ajouter une compétence (+5 XP)</option>";
    echo "</select>";
    echo "<input type='submit' value='Ajouter des points'>";
    echo "</form>";
    
    // Traiter la correction du niveau si demandée
    if (isset($_GET['fix'])) {
        $update = $conn->prepare("UPDATE user_experience SET current_level = ?, points_to_next_level = ? WHERE user_id = ?");
        $update->bind_param("iii", $calculated_level, $calculated_next_points, $user_id);
        
        if ($update->execute()) {
            echo "<p style='color: green;'>Niveau corrigé avec succès !</p>";
            echo "<p><a href='test_xp.php'>Actualiser</a></p>";
        } else {
            echo "<p style='color: red;'>Erreur lors de la correction: " . $conn->error . "</p>";
        }
    }
    
    // Traiter l'ajout de points XP
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        // Définir les points pour chaque action
        $xp_values = [
            'daily_login' => 5,
            'update_profile' => 10,
            'add_skill' => 5
        ];
        
        if (isset($xp_values[$action])) {
            $points = $xp_values[$action];
            
            // Ajouter la transaction d'XP
            $stmt = $conn->prepare("INSERT INTO experience_transactions (user_id, points, reason) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $user_id, $points, $action);
            
            if ($stmt->execute()) {
                // Mettre à jour les points
                $new_total = $xp_data['total_points'] + $points;
                $update = $conn->prepare("UPDATE user_experience SET total_points = ? WHERE user_id = ?");
                $update->bind_param("ii", $new_total, $user_id);
                
                if ($update->execute()) {
                    echo "<p style='color: green;'>Points ajoutés avec succès ! Nouveaux points: " . $new_total . "</p>";
                    echo "<p><a href='test_xp.php'>Actualiser pour vérifier le niveau</a></p>";
                } else {
                    echo "<p style='color: red;'>Erreur lors de la mise à jour des points: " . $conn->error . "</p>";
                }
            } else {
                echo "<p style='color: red;'>Erreur lors de l'ajout de la transaction: " . $conn->error . "</p>";
            }
        }
    }
} else {
    echo "<p>Aucune donnée d'expérience trouvée pour cet utilisateur.</p>";
}
?>