<?php
$host = 'localhost';
$user = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? 'root' : 'root';
$password = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? '' : 'root';
$database = 'db_HeartHive';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
