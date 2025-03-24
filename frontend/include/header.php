<?php

session_start();

include '../backend/db.php';
?>



<head>

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @layer utilities {
            .bg-custom-pink {
                background-color: #ffb3e4;
            }
        }
    </style>
</head>


<header
    class="bg-custom-pink shadow-md p-4 flex items-center justify-between absolute top-0 left-0 w-full z-10">
    <!-- Logo -->
    <div class="text-xl font-bold text-gray-800">Logo</div>

    <!-- Barre de recherche -->
    <div class="relative w-1/3">
        <input
            type="text"
            placeholder="Rechercher..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />

        <svg
            class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor">
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
    </div>

    <!-- Icônes à droite -->
    <div class="flex items-center space-x-6">
        <!-- Icône de notification -->
        <button class="relative">
            <svg
                class="w-6 h-6 text-gray-600 hover:text-gray-800"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
        </button>

        <!-- Photo de profil -->

        <a href="profile.php">
            <img
                src="<?php echo $_SESSION['user_profile_picture'] ?>"
                alt="Profil"
                class="w-10 h-10 rounded-full border border-gray-300" />

        </a>
    </div>
</header>
<div class="h-[70px]"></div>