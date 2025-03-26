<?php
session_start();

// Vérifier si l'ID est envoyé via POST
if (isset($_POST['association_id'])) {
    $_SESSION['association_id'] = $_POST['association_id']; // Stocke l'ID dans la session
    
    // Vérifier si une page de redirection est spécifiée
    if (isset($_POST['redirect'])) {
        // Rediriger vers la page spécifiée
        header("Location: " . $_POST['redirect']);
        exit;
    } else {
        // Sinon, juste confirmer
        echo "ID de l'association " . $_POST['association_id'] . " enregistré en session.";
    }
} else {
    echo "Aucun ID d'association reçu.";
}
?>