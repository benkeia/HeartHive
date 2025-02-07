<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HeartHive</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://unpkg.com/dropzone@6.0.0-beta.2/dist/dropzone-min.js"></script>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
</head>

<body>
    <h1>HeartHive</h1>

    <?php include '../backend/db.php';

    $result = $conn->query("SELECT COUNT(*) AS count FROM user");
    $row = $result->fetch_assoc();
    echo "<p>Nombre de choses dans la table user: " . $row['count'] . "</p>";

    ?>


    <script type="module" src="../node_modules/dropzone/dist/dropzone-min.js"></script>
    <script type="module" src="js/script.js"></script>
</body>

</html>