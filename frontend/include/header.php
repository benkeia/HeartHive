<?php
session_start();
include '../backend/db.php';
?>

<head>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>

<header class="bg-pink-300 p-3 absolute top-0 left-0 w-full">
    <nav class="flex flex-row justify-between items-center">
        <div class="leftheader">
            <!-- Logo -->
        </div>
        <div class="rightheader flex flex-row gap-x-10 items-center">
            <div class="searchbar flex items-center relative">
                <input class="px-4 py-2 border rounded-full w-[400px] h-2/3 focus:outline-none focus:ring-2 focus:ring-pink-500 bg-slate-100 pl-10" type="text" placeholder="Rechercher">
            </div>

            <a class="w-[30px] pointer" href="notif.php">
                <img class=" " src="assets/icons/notification.png">
            </a>
            <a class="profilepicture w-[60px] pointer" href="profile.php">
                <img class="rounded-full " src="<?php echo $_SESSION['user_profile_picture'] ?>" alt="Image de <?php echo $_SESSION['firstname'] ?>">
            </a>
        </div>
    </nav>
</header>

<!-- Div vide pour éviter que les éléments soient cachés sous le header -->
<div class="h-[90px]"></div>