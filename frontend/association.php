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
        $associationProfilePicture = htmlspecialchars($association['association']);
        $associationName = htmlspecialchars($association['association_name']);
        $associationAdress = htmlspecialchars($association['association_adress']);
        $associationBio = htmlspecialchars($association['association_bio']);
    }

    // Fermer la requête
    $associationStatement->close();
    ?>

    <div class="mainAssociationContainer flex flex-col">
        <div class="topAssociationContainer flex">
            <div class="leftAssociationContainer">
                <img src="" alt="">
                <div class="associationTitle flex gap-x-10">
                    <img src="" alt="">
                    <h1>Nom de l'assoc</h1>
                </div>
                <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Asperiores, consequatur.</p>
            </div>
            <div class="rightAssocaitionContainer">
                <h2>NOM DE LA MISSION</h2>
                <div class="associationInformationContainer flex flex-col gap-y-5">
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus in labore reprehenderit nam odit consequuntur quasi similique recusandae, quod libero!</p>
                </div>
                <hr>
                <div class="associationLocation flex gap-x-5">
                    <div class="imageLocationContainer flex flex-col rounded-2xl shadow-lg">
                        <img src="" alt="">
                        <p>Lorem ipsum dolor sit amet.</p>
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