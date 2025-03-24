<?php
session_start(); // Assure-toi que la session est bien démarrée

// Vérifier si l'ID est envoyé via POST
if (isset($_POST['association_id'])) {
    $_SESSION['association_id'] = $_POST['association_id']; // Stocke l'ID dans la session
    echo "ID de l'association enregistré en session.";
} else {
    echo "Aucun ID d'association reçu.";
}
?>