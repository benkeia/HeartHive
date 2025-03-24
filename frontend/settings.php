<?php

session_start();
include '../backend/db.php';


if (isset($_SESSION['type']) && $_SESSION['type'] == 1) {
  header('Location: profile_asso.php');
  exit;
}

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
  header('Location: loginPage.php');
  exit;
}

// Récupérer les tags de l'utilisateur s'ils existent
$userTags = isset($_SESSION['user_tags']) ? $_SESSION['user_tags'] : '{}';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'primary': '#8B5CF6',
            'primary-dark': '#7C3AED',
            'secondary': '#EC4899',
            'light-bg': '#F9FAFB',
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          },
          boxShadow: {
            'custom': '0 4px 15px rgba(0, 0, 0, 0.05)',
          }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
  <script src="js/codeProfile.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
  <title>Profile</title>
  <style>
    body {
      background-color: #F9FAFB;
      font-family: 'Inter', sans-serif;
    }

    .section-title {
      position: relative;
      margin-bottom: 1.5rem;
      font-weight: 600;
      color: #4B5563;
      padding-bottom: 0.5rem;
    }

    .section-title:after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      height: 3px;
      width: 40px;
      background: linear-gradient(to right, #8B5CF6, #EC4899);
      border-radius: 3px;
    }

    .menu-item {
      transition: all 0.2s ease;
      border-left: 3px solid transparent;
    }

    .menu-item:hover,
    .menu-item.active {
      border-left: 3px solid #8B5CF6;
      background-color: #F3F4F6;
      color: #8B5CF6;
      font-weight: 500;
    }

    .gradient-btn {
      background: #FFE9EF;
      border: 2px solid #CF3275;
      border-radius: 6px;
      color: #CF3275;
      font-weight: 500;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 10px 24px;
      transition: all 0.2s ease;
    }

    .gradient-btn:hover {
      background: #ffd6e2;
      box-shadow: 0 4px 12px rgba(207, 50, 117, 0.15);
      transform: translateY(-1px);
    }

    .gradient-btn svg {
      stroke: #CF3275;
    }

    .round-btn {
      background: #FFE9EF;
      border: 2px solid #CF3275;
      color: #CF3275;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      transition: all 0.2s ease;
    }

    .round-btn:hover {
      background: #ffd6e2;
      box-shadow: 0 4px 12px rgba(207, 50, 117, 0.15);
    }

    .round-btn svg {
      stroke: #CF3275;
    }
  </style>
</head>

<body class="bg-light-bg font-sans">

  <?php include 'include/header.php'; ?>

  <!-- Ajout des données de tags pour JS -->
  <div id="userTagsData" data-tags='<?php echo htmlspecialchars($userTags, ENT_QUOTES, 'UTF-8'); ?>' class="hidden"></div>


  <div class="flex flex-col md:flex-row gap-6">
    <!-- Menu latéral -->
    <div class="w-full md:w-1/4 bg-white rounded-xl shadow-custom overflow-hidden">
      <div class="p-5">
        <h3 class="text-xl font-bold mb-6 text-gray-800">Mon compte</h3>
        <ul class="space-y-1">
          <li class="menu-item active p-3 rounded-lg text-gray-700">Mon profil</li>
          <li class="menu-item p-3 rounded-lg text-gray-700">Mes engagements</li>
          <li class="menu-item p-3 rounded-lg text-gray-700">Statistiques</li>
          <li class="menu-item p-3 rounded-lg text-gray-700">Certifications</li>
          <li class="menu-item p-3 rounded-lg text-gray-700">Messagerie</li>
          <li class="menu-item p-3 rounded-lg text-gray-700">Paramètres</li>
        </ul>
      </div>
    </div>

    <!-- Contenu principal -->
    <div class="w-full md:w-3/4">
      <div class="bg-white rounded-xl shadow-custom p-6">
        <div class="pt-4">
          <a href="disconnect.php" class="inline-flex items-center justify-center px-6 py-3 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Se déconnecter
          </a>
        </div>
      </div>
    </div>
  </div>


  <script>
    // Activer le premier élément du menu par défaut
    document.querySelector('.menu-item').classList.add('active');
  </script>

</body>

</html>