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
    include 'include/header.php';

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
            <div class="leftAssociationContainer p-10">
                <img class="rounded-3xl" src="<?php echo $associationBackgroundImage?>" alt="">
                <div class="associationTitle flex gap-x-10 items-center py-10">
                    <img class="w-[75px] rounded-full"src="<?php echo $associationProfilePicture?>" alt="">
                    <h1 class="text-xl"><?php echo $associationName?></h1>
                </div>
                <p><?php echo $associationDesc?></p>
            </div>
            <div class="rightAssocaitionContainer p-10 flex flex-col gap-y-5">
                <h2 class="text-5xl">NOM DE LA MISSION</h2>
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
                        <button class="py-2 px-5 bg-blue-100 rounded-3xl cursor-pointer hover:bg-blue-200 ">Postuler</button>
                        <p class="text-sm text-slate-400">En vous inscrivant à cette association, vous acceptez de respecter les conditions suivantes : participer activement aux missions, respecter les autres membres et suivre les directives de l'association. Merci pour votre engagement.</p>
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