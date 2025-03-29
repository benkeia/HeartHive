<?php
// filepath: /Applications/MAMP/htdocs/HeartHive/frontend/landing.php
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    // Check user type and redirect accordingly
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 1) {
        header("Location: index_asso.php");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HeartHive - Connectons le cœur du bénévolat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .hero-section {
            position: relative;
            background-color: rgba(0, 0, 0, 0.5);
            height: 80vh;
            overflow: hidden;
        }

        .hero-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('uploads/missions/asso.avif');
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            -webkit-filter: blur(8px);
            z-index: -1;
        }

        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .button-pink {
            background-color: #ffb3e4;
            transition: all 0.3s ease;
        }

        .button-pink:hover {
            background-color: #ff85d5;
        }

        .button-outline {
            border: 2px solid #ffb3e4;
            color: #ffb3e4;
            transition: all 0.3s ease;
        }

        .button-outline:hover {
            background-color: #ffb3e4;
            color: white;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-md fixed w-full top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <img src="assets/img/LogoHeader.png" alt="HeartHive Logo" class="h-10 mr-3">
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <a href="#comment-ca-marche" class="text-gray-700 hover:text-pink-600">Comment ça marche</a>
                <a href="#pour-associations" class="text-gray-700 hover:text-pink-600">Pour les associations</a>
                <a href="#pour-benevoles" class="text-gray-700 hover:text-pink-600">Pour les bénévoles</a>
                <a href="loginPage.php" class="px-4 py-2 button-outline rounded-full">Se connecter</a>
                <a href="signup.php" class="px-4 py-2 button-pink text-white rounded-full">S'inscrire</a>
            </div>
            <button class="md:hidden text-gray-700" id="mobile-menu-button">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
        <!-- Menu mobile -->
        <div id="mobile-menu" class="hidden md:hidden bg-white w-full border-t border-gray-200">
            <div class="container mx-auto px-4 py-2 flex flex-col space-y-2">
                <a href="#comment-ca-marche" class="py-2 text-gray-700">Comment ça marche</a>
                <a href="#pour-associations" class="py-2 text-gray-700">Pour les associations</a>
                <a href="#pour-benevoles" class="py-2 text-gray-700">Pour les bénévoles</a>
                <div class="flex flex-col space-y-2 pt-2 border-t border-gray-200">
                    <a href="loginPage.php" class="px-4 py-2 border border-pink-400 text-pink-600 rounded-full text-center">Se connecter</a>
                    <a href="signup.php" class="px-4 py-2 button-pink text-white rounded-full text-center">S'inscrire</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Section Hero -->
    <section class="hero-section flex items-center justify-center text-white mt-16">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-4 fade-in">Connectons le cœur du bénévolat</h1>
            <p class="text-xl md:text-2xl mb-8 fade-in">La plateforme qui rapproche les associations des bénévoles passionnés</p>
            <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4 justify-center fade-in">
                <a href="signup.php" class="px-8 py-3 button-pink text-white text-lg font-semibold rounded-full">
                    Rejoindre HeartHive
                </a>
                <a href="loginPage.php" class="px-8 py-3 bg-white text-gray-800 text-lg font-semibold rounded-full hover:bg-gray-100">
                    Se connecter
                </a>
            </div>
        </div>
    </section>

    <!-- Comment ça marche -->
    <section id="comment-ca-marche" class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-12">Comment ça marche ?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gray-50 p-6 rounded-lg shadow-md text-center">
                    <div class="w-16 h-16 bg-pink-100 text-pink-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-plus text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">1. Inscrivez-vous</h3>
                    <p class="text-gray-600">Créez votre compte en quelques clics et complétez votre profil.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg shadow-md text-center">
                    <div class="w-16 h-16 bg-pink-100 text-pink-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-search text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">2. Trouvez votre mission</h3>
                    <p class="text-gray-600">Parcourez les missions disponibles ou publiez votre besoin en bénévoles.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg shadow-md text-center">
                    <div class="w-16 h-16 bg-pink-100 text-pink-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-handshake text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">3. Connectez-vous</h3>
                    <p class="text-gray-600">Entrez en contact et commencez votre collaboration solidaire.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pour les associations -->
    <section id="pour-associations" class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-8 md:mb-0">
                    <img src="uploads/missions/asso.avif" alt="Associations" class="rounded-lg shadow-lg max-w-full h-auto">
                </div>
                <div class="md:w-1/2 md:pl-10">
                    <h2 class="text-3xl font-bold mb-6">Pour les associations</h2>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 text-pink-600 mt-1">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <p class="ml-3 text-lg text-gray-700">Publiez vos missions de bénévolat en quelques clics</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 text-pink-600 mt-1">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <p class="ml-3 text-lg text-gray-700">Trouvez des bénévoles qualifiés selon vos besoins</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 text-pink-600 mt-1">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <p class="ml-3 text-lg text-gray-700">Gérez efficacement vos équipes de volontaires</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 text-pink-600 mt-1">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <p class="ml-3 text-lg text-gray-700">Augmentez la visibilité de vos actions solidaires</p>
                        </li>
                    </ul>
                    <div class="mt-8">
                        <a href="signup.php?type=association" class="px-6 py-3 button-pink text-white rounded-full inline-block">
                            Inscrire mon association
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pour les bénévoles -->
    <section id="pour-benevoles" class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row-reverse items-center">
                <div class="md:w-1/2 mb-8 md:mb-0">
                    <img src="uploads/missions/default.jpg" alt="Bénévoles" class="rounded-lg shadow-lg max-w-full h-auto">
                </div>
                <div class="md:w-1/2 md:pr-10">
                    <h2 class="text-3xl font-bold mb-6">Pour les bénévoles</h2>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 text-pink-600 mt-1">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <p class="ml-3 text-lg text-gray-700">Découvrez des missions qui correspondent à vos intérêts</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 text-pink-600 mt-1">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <p class="ml-3 text-lg text-gray-700">Postulez facilement aux opportunités de bénévolat</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 text-pink-600 mt-1">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <p class="ml-3 text-lg text-gray-700">Valorisez vos compétences et votre engagement</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 text-pink-600 mt-1">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <p class="ml-3 text-lg text-gray-700">Rejoignez une communauté solidaire et engagée</p>
                        </li>
                    </ul>
                    <div class="mt-8">
                        <a href="signup.php?type=volunteer" class="px-6 py-3 button-pink text-white rounded-full inline-block">
                            Devenir bénévole
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- CTA -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold mb-4">Prêt à rejoindre l'aventure HeartHive ?</h2>
            <p class="text-xl text-gray-700 mb-8">Que vous soyez une association ou un bénévole, HeartHive vous aide à créer des liens solidaires.</p>
            <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4 justify-center">
                <a href="signup.php" class="px-8 py-3 button-pink text-white text-lg font-semibold rounded-full">
                    Rejoindre HeartHive
                </a>
                <a href="loginPage.php" class="px-8 py-3 bg-white text-gray-800 text-lg font-semibold rounded-full border border-gray-300 hover:bg-gray-100">
                    Se connecter
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-10">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">HeartHive</h3>
                    <p class="text-gray-400">Trouvez votre place par l'action.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Navigation</h3>
                    <ul class="space-y-2">
                        <li><a href="#comment-ca-marche" class="text-gray-400 hover:text-white">Comment ça marche</a></li>
                        <li><a href="#pour-associations" class="text-gray-400 hover:text-white">Pour les associations</a></li>
                        <li><a href="#pour-benevoles" class="text-gray-400 hover:text-white">Pour les bénévoles</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Nous contacter</h3>
                    <ul class="space-y-2">
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2 text-gray-400"></i>
                            <a href="mailto:contact@hearthive.fr" class="text-gray-400 hover:text-white">contact@hearthive.fr</a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-2 text-gray-400"></i>
                            <span class="text-gray-400">01 23 45 67 89</span>
                        </li>
                    </ul>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center">
                <p class="text-gray-400">&copy; 2025 HeartHive. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        // Menu mobile toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Navigation smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();

                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });

                    // Hide mobile menu if open
                    document.getElementById('mobile-menu').classList.add('hidden');
                }
            });
        });
    </script>
</body>

</html>