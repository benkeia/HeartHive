<?php
// Inclure la connexion MySQLi
include '../backend/db.php';

if (!isset($_GET['q']) || empty($_GET['q'])) {
    echo json_encode(["error" => "Aucune recherche fournie"]);
    exit;
}

$search = $conn->real_escape_string($_GET['q']);

$query = "SELECT * FROM association WHERE association_name LIKE '%$search%' LIMIT 5";
$result = $conn->query($query);

$associations = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $associations[] = $row;
    }
}

echo json_encode($associations);
?>
