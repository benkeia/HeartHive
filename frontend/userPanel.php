<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="frontend/assets/css/style.css">
</head>
<body>
<!--Insert here the future header php with include-->
<?php
include '../backend/db.php';

// Requête préparée
$usersStatement = $conn->prepare("SELECT * FROM user WHERE user_id = ?");
$usersStatement->bind_param("i", $userId);

// Remplacez 0 par la valeur souhaitée
$userId = 1;
$usersStatement->execute();

// Récupération des résultats
$result = $usersStatement->get_result();
while ($user = $result->fetch_assoc()) {
   $userProfilePicture = htmlspecialchars($user['user_profile_picture']);
   $userName = htmlspecialchars($user['user_name']);
   $userAdress = htmlspecialchars($user['user_adress']);
   $userBio = htmlspecialchars($user['user_bio']);
}

// Fermer la requête
$usersStatement->close();

?>
<div class="mainContainer flex justify-between">
    <div class="leftUserContainer shadow-lg rounded-3xl p-10">
        <h1 class="text-2xl my-5" >Mon compte</h1>
        <ul class="flex flex-col gap-y-2">
            <li><a href="#">Mon profil</a></li><hr>
            <li><a href="#">Mes engagements</a></li><hr>
            <li><a href="#">Statistiques</a></li><hr>
            <li><a href="#">Certifications</a></li><hr>
            <li><a href="#">Messagerie</a></li><hr>
            <li><a href="#">Paramètres</a></li><hr>
        </ul>
    </div>

    <div class="rightUserContainer shadow-lg rounded-3xl w-2/3 p-10">
        <div class="userInterfaceTop flex">
            <div class="userPicture w-1/3">
                <img src="<?php echo $userProfilePicture?>" alt="" class="rounded-full">
            </div>
            <div class="userTitle mx-10 flex flex-col gap-y-5">
                <h2 class="text-3xl"><?php echo $userName?></h2>
                <p><?php echo $userAdress?></p>
                <p><?php echo $userBio?></p>
            </div>
        </div>
        <div class="userInterfaceBottom flex justify-between py-10">
            <div class="userInterest">
                <h3 class="text-2xl">Centres d'intérêts</h3>
            </div>
            <div class="userDisponibility">
                <h3 class="text-2xl">Disponibilités</h3>
            </div>
        </div>
    </div>
</div>

</div>
</body>
</html>