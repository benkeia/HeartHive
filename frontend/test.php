<?php
session_start();
require_once '../backend/db.php';

// Récupérer l'ID utilisateur
$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);

// Si aucun ID utilisateur n'est fourni, utiliser l'ID 1 pour les tests
if (empty($userId)) {
  $userId = 1;
}

// Récupérer les informations de l'utilisateur
$stmt = $conn->prepare("SELECT user_firstname, user_name, user_disponibility FROM user WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  $userName = htmlspecialchars($row['user_firstname'] . ' ' . $row['user_name']);
  $availabilities = [];

  if (!empty($row['user_disponibility'])) {
    $availabilities = json_decode($row['user_disponibility'], true);
  }
} else {
  echo "Utilisateur introuvable";
  exit;
}

$stmt->close();

// Jours et heures pour l'affichage
$days = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
$hours = [];
for ($i = 8; $i <= 21; $i++) {
  $hours[] = $i . ':00';
}

// Fonction pour vérifier si un créneau horaire est disponible
function isAvailable($day, $hour, $availabilities)
{
  if (empty($availabilities)) return false;

  foreach ($availabilities as $dayData) {
    if ($dayData['day'] === $day && in_array($hour, $dayData['hours'])) {
      return true;
    }
  }

  return false;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Disponibilités de <?php echo $userName; ?></title>
  <style>
    :root {
      --primary: #6366f1;
      --primary-light: #a5b4fc;
      --primary-dark: #4f46e5;
      --primary-bg: #eef2ff;
      --gray-50: #f9fafb;
      --gray-100: #f3f4f6;
      --gray-200: #e5e7eb;
      --gray-300: #d1d5db;
      --gray-400: #9ca3af;
      --gray-500: #6b7280;
      --gray-700: #374151;
      --gray-800: #1f2937;
      --text: #111827;
      --selected: #c7d2fe;
      --selected-border: #818cf8;
      --white: #ffffff;
      --radius: 8px;
      --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
      --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
    }

    body {
      background-color: var(--gray-50);
      color: var(--text);
      padding: 12px;
      min-height: 100vh;
    }

    .container {
      max-width: 400px;
      margin: 0 auto;
      background-color: var(--white);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
      font-size: 0.75rem;
    }

    .component-title {
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--gray-800);
      margin-bottom: 0.25rem;
      padding: 8px 12px;
      border-bottom: 1px solid var(--gray-200);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .schedule-container {
      background-color: var(--white);
      overflow-x: auto;
      position: relative;
      max-height: 280px;
    }

    .day-headers {
      display: grid;
      grid-template-columns: 30px repeat(7, 1fr);
      position: sticky;
      top: 0;
      z-index: 5;
      background-color: var(--white);
      box-shadow: var(--shadow-sm);
      width: 100%;
    }

    .day-header {
      padding: 5px 0;
      text-align: center;
      font-weight: 600;
      border-bottom: 1px solid var(--gray-200);
      color: var(--gray-700);
      font-size: 0.65rem;
      display: flex;
      align-items: center;
      justify-content: center;
      border-right: 1px solid var(--gray-200);
    }

    .schedule {
      display: grid;
      grid-template-columns: 30px repeat(7, 1fr);
      width: 100%;
    }

    .time-column {
      grid-column: 1;
      background-color: var(--gray-50);
      border-right: 1px solid var(--gray-200);
      z-index: 1;
    }

    .time-label {
      height: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.6rem;
      color: var(--gray-500);
      border-bottom: 1px solid var(--gray-200);
      position: relative;
      padding-right: 3px;
    }

    .time-label::after {
      content: '';
      position: absolute;
      width: 4px;
      height: 1px;
      right: 0;
      background-color: var(--gray-300);
    }

    .day-column {
      display: grid;
      grid-template-rows: repeat(14, 20px);
      position: relative;
    }

    .day-column::before {
      content: '';
      position: absolute;
      top: 0;
      bottom: 0;
      width: 100%;
      background: repeating-linear-gradient(to bottom,
          transparent,
          transparent 9px,
          var(--gray-100) 9px,
          var(--gray-100) 10px,
          transparent 10px,
          transparent 19px,
          var(--gray-200) 19px,
          var(--gray-200) 20px);
      pointer-events: none;
    }

    .time-slot {
      height: 20px;
      border-bottom: 1px solid var(--gray-200);
      border-right: 1px solid var(--gray-200);
      position: relative;
      z-index: 1;
    }

    .time-slot.available {
      background-color: var(--selected);
      box-shadow: inset 0 0 0 1px var(--selected-border);
    }

    .current-hour-indicator {
      position: absolute;
      height: 1px;
      background-color: #ef4444;
      width: 100%;
      z-index: 2;
      pointer-events: none;
    }

    .current-hour-indicator::before {
      content: '';
      position: absolute;
      left: 0;
      top: -2px;
      width: 4px;
      height: 4px;
      border-radius: 50%;
      background-color: #ef4444;
    }

    .summary {
      background-color: var(--gray-50);
      padding: 8px 12px;
      font-size: 0.7rem;
      border-top: 1px solid var(--gray-200);
      max-height: 100px;
      overflow-y: auto;
    }

    .summary-title {
      font-weight: 600;
      margin-bottom: 6px;
      color: var(--gray-800);
      font-size: 0.7rem;
    }

    .summary-list {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
    }

    .summary-item {
      background-color: var(--white);
      padding: 3px 8px;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      border: 1px solid var(--gray-200);
      color: var(--gray-700);
      box-shadow: var(--shadow-sm);
      font-size: 0.65rem;
    }

    .legend {
      display: flex;
      margin: 0 12px 6px;
      gap: 10px;
      font-size: 0.65rem;
      align-items: center;
    }

    .legend-item {
      display: flex;
      align-items: center;
      gap: 4px;
      color: var(--gray-700);
    }

    .legend-color {
      width: 8px;
      height: 8px;
      border-radius: 2px;
    }

    .legend-color.available {
      background-color: var(--selected);
      border: 1px solid var(--selected-border);
    }

    .legend-color.unavailable {
      background-color: var(--white);
      border: 1px solid var(--gray-300);
    }

    .actions {
      padding: 8px 12px;
      display: flex;
      justify-content: center;
      background-color: var(--gray-50);
      border-top: 1px solid var(--gray-200);
    }

    .btn-edit {
      padding: 4px 8px;
      border-radius: 4px;
      font-weight: 500;
      font-size: 0.7rem;
      cursor: pointer;
      background: linear-gradient(to right, var(--primary), var(--primary-dark));
      border: none;
      color: white;
      box-shadow: 0 1px 3px rgba(79, 70, 229, 0.3);
      transition: all 0.2s;
    }

    .btn-edit:hover {
      box-shadow: 0 2px 4px rgba(79, 70, 229, 0.4);
      transform: translateY(-1px);
    }

    @media (max-width: 400px) {
      body {
        padding: 0;
      }

      .container {
        border-radius: 0;
        max-width: 100%;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="component-title">
      <span>Disponibilités de <?php echo $userName; ?></span>
      <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId): ?>
        <button class="btn-edit" onclick="window.location.href='edit_availability.php'">Modifier</button>
      <?php endif; ?>
    </div>

    <div class="legend">
      <div class="legend-item">
        <div class="legend-color available"></div>
        <span>Disponible</span>
      </div>
      <div class="legend-item">
        <div class="legend-color unavailable"></div>
        <span>Indisponible</span>
      </div>
    </div>

    <div class="schedule-container">
      <div class="day-headers">
        <div class="day-header"></div>
        <?php foreach ($days as $day): ?>
          <div class="day-header"><?php echo $day; ?></div>
        <?php endforeach; ?>
      </div>

      <div class="schedule">
        <div class="time-column">
          <?php foreach ($hours as $hour): ?>
            <div class="time-label"><?php echo $hour; ?></div>
          <?php endforeach; ?>
        </div>

        <?php foreach ($days as $day): ?>
          <div class="day-column">
            <?php foreach ($hours as $hour): ?>
              <div class="time-slot <?php echo isAvailable($day, $hour, $availabilities) ? 'available' : ''; ?>"></div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="summary">
      <div class="summary-title">Récapitulatif des disponibilités :</div>
      <div class="summary-list">
        <?php
        $hasSummary = false;
        foreach ($availabilities as $dayData):
          $day = $dayData['day'];
          $dayHours = $dayData['hours'];

          // Regrouper les heures consécutives
          usort($dayHours, function ($a, $b) {
            return intval(explode(':', $a)[0]) - intval(explode(':', $b)[0]);
          });

          $ranges = [];
          $start = null;
          $end = null;
          $hourIndices = array_map(function ($hour) use ($hours) {
            return array_search($hour, $hours);
          }, $dayHours);
          sort($hourIndices);

          if (!empty($hourIndices)) {
            $start = $end = $hourIndices[0];
            for ($i = 1; $i < count($hourIndices); $i++) {
              if ($hourIndices[$i] === $end + 1) {
                $end = $hourIndices[$i];
              } else {
                $ranges[] = ['start' => $start, 'end' => $end];
                $start = $end = $hourIndices[$i];
              }
            }
            $ranges[] = ['start' => $start, 'end' => $end];

            $hasSummary = true;
            $rangeText = '';

            foreach ($ranges as $index => $range) {
              $startHour = $hours[$range['start']];
              $endHourValue = intval(explode(':', $hours[$range['end']])[0]) + 1;
              $endHour = $endHourValue . ':00';
              $rangeText .= $startHour . ' - ' . $endHour;
              if ($index < count($ranges) - 1) $rangeText .= ', ';
            }
        ?>
            <div class="summary-item"><?php echo $day . ': ' . $rangeText; ?></div>
          <?php
          }
        endforeach;

        if (!$hasSummary):
          ?>
          <div style="color: var(--gray-500);">Aucune disponibilité indiquée</div>
        <?php endif; ?>
      </div>
    </div>

  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Ajouter l'indicateur de l'heure actuelle
      function addCurrentTimeIndicator() {
        const now = new Date();
        const currentHour = now.getHours();
        const currentMinute = now.getMinutes();

        // Vérifier si l'heure actuelle est dans notre plage (8h-21h)
        if (currentHour >= 8 && currentHour <= 21) {
          const hourIndex = currentHour - 8;
          const minutePercentage = currentMinute / 60;
          const topPosition = (hourIndex + minutePercentage) * 20;

          const indicator = document.createElement('div');
          indicator.className = 'current-hour-indicator';
          indicator.style.top = `${topPosition}px`;

          // Ajouter l'indicateur à chaque colonne de jour
          document.querySelectorAll('.day-column').forEach(column => {
            column.appendChild(indicator.cloneNode(true));
          });
        }
      }

      addCurrentTimeIndicator();
    });
  </script>
</body>

</html>