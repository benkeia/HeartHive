<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="frontend/assets/css/style.css">
</head>

<body>
    <?php
    include '../backend/db.php';

    // Requête préparée
    $associationStatement = $conn->prepare("SELECT * FROM association WHERE association_id = ?");
    $associationStatement->bind_param("i", $associationId);

    // Remplacez 0 par la valeur souhaitée
    $associationId = 1;
    $associationStatement->execute();

    // Récupération des résultats
    $result = $associationStatement->get_result();
    while ($association = $result->fetch_assoc()) {
        $associationProfilePicture = htmlspecialchars($association['association_profile_picture']);
        $associationBackgroundImage = htmlspecialchars($association['association_background_image']);
        $associationName = htmlspecialchars($association['association_name']);
        $associationAdress = htmlspecialchars($association['association_adress']);
        $associationDesc = htmlspecialchars($association['association_desc']);
        $associationMission = htmlspecialchars($association['association_mission']);
    }

    // Fermer la requête
    $associationStatement->close();
    ?>

    <div class="mainAssociationContainer flex flex-col">
        <div class="topAssociationContainer flex">
            <div class="leftAssociationContainer">
                <img src="<?php echo $associationBackgroundImage?>" alt="">
                <div class="associationTitle flex gap-x-10">
                    <img src="<?php echo $associationProfilePicture?>" alt="">
                    <h1><?php echo $associationName?></h1>
                </div>
                <p><?php echo $associationDesc?></p>
            </div>
            <div class="rightAssocaitionContainer">
                <h2>NOM DE LA MISSION</h2>
                <div class="associationInformationContainer flex flex-col gap-y-5">
                    <p><?php echo $associationMission?></p>
                </div>
                <hr>
                <div class="associationLocation flex gap-x-5">
                    <div class="imageLocationContainer flex flex-col rounded-2xl shadow-lg">
                        <img src="" alt="">
                        <p><?php echo $associationAdress?></p>
                    </div>
                    <div class="buttonPostulateContainer">
                        <button>Postuler</button>
                        <p class="text-sm text-slate-400">Lorem ipsum dolor sit amet consectetur adipisicing elit. Aut magnam sed praesentium unde facere adipisci, doloribus nemo! Esse, quaerat quas et voluptates nulla in, enim omnis nihil inventore, exercitationem quos.</p>
                    </div>
                </div>
                <hr>
            </div>
        </div>
        <div class="bottomAssociationContainer">
            <div class="disponibilityContainer">

            </div>
        </div>
    </div>

</body>

</html>