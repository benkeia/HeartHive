<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HeartHive</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <h1>HeartHive</h1>

    <?php include '../backend/db.php';

    $result = $conn->query("SELECT COUNT(*) AS count FROM user");
    $row = $result->fetch_assoc();
    echo "<p>Nombre de choses dans la table user: " . $row['count'] . "</p>";

    ?>



    <script type="module" src="js/script.js"></script>
</body>

</html>