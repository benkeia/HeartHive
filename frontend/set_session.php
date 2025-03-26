<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['association_id'])) {
        $_SESSION['association_id'] = $_POST['association_id'];
        echo "Association ID set in session";
    } elseif (isset($_POST['mission_id'])) {
        $_SESSION['mission_id'] = $_POST['mission_id'];
        echo "Mission ID set in session";
    } else {
        echo "No valid ID provided";
    }
} else {
    echo "Invalid request method";
}
