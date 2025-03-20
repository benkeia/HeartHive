<?php
session_start();
include '../backend/db.php';
?>
<head>
<script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>

<header class="bg-pink-300 p-5">
    <nav class="flex flex-row justify-between">
        <div class="leftheader">
            <!-- Logo -->
        </div>
        <div class="rightheader flex flex-row gap-x-10">
                <div class="searchbar flex items-center">
                    <input class="px-4 py-2 border rounded-full w-[400px] h-2/3 focus:outline-none focus:ring-2 focus:ring-pink-500 bg-slate-100" type="text" placeholder="Rechercher">
                </div>
                <a class="profilepicture w-[75px] pointer" href="profile.php">
                    <img class="rounded-full "src="<?php echo $_SESSION['user_profile_picture']?>" alt="Image de ".<?php echo $_SESSION['firstname']?>>
                </a>
        </div>
    </nav>
</header>