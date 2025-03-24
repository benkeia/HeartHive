<?php


// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion
    // header('Location: login.php');
    // exit();
    $userId = 1; // Pour les tests, utilisez l'ID 1
} else {
    $userId = $_SESSION['user_id'];
}

// Récupérer les disponibilités de l'utilisateur
$userAvailabilities = [];
$stmt = $conn->prepare("SELECT user_disponibility FROM user WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (!empty($row['user_disponibility'])) {
        $userAvailabilities = json_decode($row['user_disponibility'], true);
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier vos disponibilités</title>
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
            --selected-hover: #a5b4fc;
            --selected-border: #818cf8;
            --white: #ffffff;
            --radius: 8px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
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
            max-width: 100%;
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
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
            z-index: 1;
        }

        .time-slot:hover {
            background-color: var(--primary-bg);
        }

        .time-slot.selected {
            background-color: var(--selected);
            box-shadow: inset 0 0 0 1px var(--selected-border);
        }

        .time-slot.selected:hover {
            background-color: var(--selected-hover);
        }

        .actions {
            padding: 8px 12px;
            display: flex;
            justify-content: space-between;
            gap: 6px;
            background-color: var(--gray-50);
            border-top: 1px solid var(--gray-200);
        }

        button {
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.2s;
            outline: none;
        }

        .btn-secondary {
            background-color: var(--white);
            border: 1px solid var(--gray-300);
            color: var(--gray-700);
            box-shadow: var(--shadow-sm);
        }

        .btn-secondary:hover {
            background-color: var(--gray-100);
            border-color: var(--gray-400);
        }

        .btn-cancel {
            background-color: var(--white);
            border: 1px solid var(--gray-300);
            color: var(--gray-700);
            box-shadow: var(--shadow-sm);
        }

        .btn-cancel:hover {
            background-color: var(--gray-100);
            border-color: var(--gray-400);
        }

        .btn-primary {
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            border: none;
            color: white;
            box-shadow: 0 1px 3px rgba(79, 70, 229, 0.3);
        }

        .btn-primary:hover {
            box-shadow: 0 2px 4px rgba(79, 70, 229, 0.4);
            transform: translateY(-1px);
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

        .status-message {
            padding: 5px 12px;
            margin-top: 5px;
            color: var(--gray-700);
            font-size: 0.7rem;
            text-align: center;
            border-radius: 4px;
            transition: opacity 0.5s ease;
            opacity: 0;
        }

        .status-message.success {
            background-color: #d1fae5;
            color: #065f46;
            opacity: 1;
        }

        .status-message.error {
            background-color: #fee2e2;
            color: #b91c1c;
            opacity: 1;
        }

        .btn-help {
            font-size: 0.6rem;
            color: var(--gray-500);
            padding: 2px 6px;
            background: var(--gray-100);
            border: 1px solid var(--gray-300);
            border-radius: 3px;
            cursor: help;
            margin-left: 5px;
        }

        @media (max-width: 400px) {
            body {
                padding: 0;
            }

            .container {
                border-radius: 0;
                max-width: 100%;
            }

            .actions {
                flex-direction: column;
            }

            .actions button {
                width: 100%;
            }
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4);
            }

            70% {
                box-shadow: 0 0 0 2px rgba(99, 102, 241, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(99, 102, 241, 0);
            }
        }

        .time-slot.just-selected {
            animation: pulse 0.6s;
        }

        .tooltip {



            position: absolute;
            background-color: var(--gray-800);
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 0.65rem;
            z-index: 10;
            opacity: 0;
            transition: opacity 0.2s;
            pointer-events: none;
            width: 240px;
            margin-top: 5px;


            box-shadow: var(--shadow);
        }


        .tooltip.show {
            opacity: 1;
        }

        .tooltip::before {
            content: '';
            position: absolute;
            top: -4px;
            left: 10px;
            width: 8px;
            height: 8px;
            background-color: var(--gray-800);
            transform: rotate(45deg);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="component-title">
            Modifiez vos disponibilités
            <button class="btn-help" id="helpBtn">?</button>
            <div class="tooltip" id="tooltip">
                Cliquez sur les cases pour indiquer vos disponibilités. Vous pouvez aussi faire glisser pour sélectionner plusieurs créneaux à la fois dans une même journée.
            </div>
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
            <div class="day-headers" id="day-headers">
                <div class="day-header"></div>
                <!-- Les en-têtes des jours seront générés par JavaScript -->
            </div>

            <div class="schedule" id="schedule">
                <!-- Tout le contenu sera généré par JavaScript -->
            </div>
        </div>

        <div class="summary">
            <div class="summary-title">Récapitulatif de vos disponibilités :</div>
            <div class="summary-list" id="summary-list">
                <!-- Les éléments de récapitulatif seront générés par JavaScript -->
            </div>
        </div>

        <div id="status-message" class="status-message"></div>

        <div class="actions">
            <button class="btn-secondary" id="clear-btn">Tout effacer</button>
            <button class="btn-primary" id="save-btn">Enregistrer</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const schedule = document.getElementById('schedule');
            const dayHeaders = document.getElementById('day-headers');
            const summaryList = document.getElementById('summary-list');
            const clearBtn = document.getElementById('clear-btn');
            const saveBtn = document.getElementById('save-btn');
            const cancelBtn = document.getElementById('cancel-btn');
            const statusMessage = document.getElementById('status-message');
            const helpBtn = document.getElementById('helpBtn');
            const tooltip = document.getElementById('tooltip');

            const days = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
            const hours = [];

            // Afficher/masquer l'infobulle d'aide
            helpBtn.addEventListener('mouseenter', function() {
                tooltip.classList.add('show');
            });

            helpBtn.addEventListener('mouseleave', function() {
                tooltip.classList.remove('show');
            });

            // Récupérer les disponibilités existantes depuis PHP
            const savedAvailabilities = <?php echo !empty($userAvailabilities) ? json_encode($userAvailabilities) : '[]'; ?>;

            // Générer les heures de 8h à 21h
            for (let i = 8; i <= 21; i++) {
                hours.push(i + ':00');
            }

            // Créer les en-têtes des jours (en abrégé pour gagner de la place)
            days.forEach(day => {
                const dayHeader = document.createElement('div');
                dayHeader.className = 'day-header';
                dayHeader.textContent = day;
                dayHeaders.appendChild(dayHeader);
            });

            // Créer la colonne des heures
            const timeColumn = document.createElement('div');
            timeColumn.className = 'time-column';
            schedule.appendChild(timeColumn);

            // Remplir la colonne des heures
            hours.forEach(hour => {
                const timeLabel = document.createElement('div');
                timeLabel.className = 'time-label';
                timeLabel.textContent = hour;
                timeColumn.appendChild(timeLabel);
            });

            // Ajouter indicateur de l'heure actuelle
            function addCurrentTimeIndicator() {
                const now = new Date();
                const currentHour = now.getHours();
                const currentMinute = now.getMinutes();
                const currentDay = now.getDay(); // 0 (Dimanche) à 6 (Samedi)

                // Vérifier si l'heure actuelle est dans notre plage (8h-21h)
                if (currentHour >= 8 && currentHour <= 21) {
                    const hourIndex = currentHour - 8;
                    const minutePercentage = currentMinute / 60;
                    const topPosition = (hourIndex + minutePercentage) * 20;

                    const indicator = document.createElement('div');
                    indicator.className = 'current-hour-indicator';
                    indicator.style.top = `${topPosition}px`;

                    // Ajouter l'indicateur uniquement à la colonne du jour actuel
                    const dayColumn = document.querySelector(`.day-column[data-day="${days[currentDay - 1]}"]`);
                    if (dayColumn) {
                        dayColumn.appendChild(indicator);
                    }
                }
            }

            // Créer les colonnes des jours avec leurs créneaux horaires
            days.forEach((day, dayIndex) => {
                const dayColumn = document.createElement('div');
                dayColumn.className = 'day-column';
                dayColumn.dataset.day = day;
                schedule.appendChild(dayColumn);

                // Ajouter les créneaux horaires pour ce jour
                hours.forEach((hour, hourIndex) => {
                    const timeSlot = document.createElement('div');
                    timeSlot.className = 'time-slot';
                    timeSlot.dataset.day = day;
                    timeSlot.dataset.hour = hour;
                    timeSlot.dataset.dayIndex = dayIndex;
                    timeSlot.dataset.hourIndex = hourIndex;
                    dayColumn.appendChild(timeSlot);
                });
            });

            // Ajouter l'indicateur de l'heure actuelle
            addCurrentTimeIndicator();

            // Fonction pour afficher un message de statut
            function showStatusMessage(message, type = 'success') {
                statusMessage.textContent = message;
                statusMessage.className = `status-message ${type}`;

                // Masquer après 3 secondes
                setTimeout(() => {
                    statusMessage.className = 'status-message';
                }, 3000);
            }

            // Pré-sélectionner les disponibilités enregistrées
            if (savedAvailabilities && savedAvailabilities.length > 0) {
                savedAvailabilities.forEach(dayData => {
                    const dayIndex = days.indexOf(dayData.day);
                    if (dayIndex !== -1) {
                        dayData.hours.forEach(hour => {
                            const hourIndex = hours.indexOf(hour);
                            if (hourIndex !== -1) {
                                const slot = document.querySelector(`.time-slot[data-day-index="${dayIndex}"][data-hour-index="${hourIndex}"]`);
                                if (slot) {
                                    slot.classList.add('selected');
                                }
                            }
                        });
                    }
                });
                updateSummary();
            }

            // Variables pour gérer le drag and drop
            let isSelecting = false;
            let isDeselecting = false;
            let startDayIndex = null;
            let startHourIndex = null;

            // Gestionnaire d'événements pour les créneaux
            document.querySelectorAll('.time-slot').forEach(slot => {
                // Événement mousedown (début de sélection)
                slot.addEventListener('mousedown', function(e) {
                    e.preventDefault(); // Empêcher la sélection de texte
                    isSelecting = !this.classList.contains('selected');
                    isDeselecting = this.classList.contains('selected');

                    startDayIndex = parseInt(this.dataset.dayIndex);
                    startHourIndex = parseInt(this.dataset.hourIndex);

                    // Basculer l'état du créneau avec animation
                    if (isSelecting) {
                        this.classList.add('selected');
                        this.classList.add('just-selected');
                        setTimeout(() => {
                            this.classList.remove('just-selected');
                        }, 600);
                    } else {
                        this.classList.remove('selected');
                    }

                    updateSummary();
                });

                // Événement mouseenter (pendant le drag)
                slot.addEventListener('mouseenter', function() {
                    if (isSelecting || isDeselecting) {
                        const currentDayIndex = parseInt(this.dataset.dayIndex);
                        const currentHourIndex = parseInt(this.dataset.hourIndex);

                        // Vérifier si on est sur la même journée (drag vertical uniquement)
                        if (currentDayIndex === startDayIndex) {
                            // Déterminer la plage horaire à sélectionner
                            const minHourIndex = Math.min(startHourIndex, currentHourIndex);
                            const maxHourIndex = Math.max(startHourIndex, currentHourIndex);

                            // Sélectionner ou désélectionner tous les créneaux dans cette plage
                            document.querySelectorAll(`.time-slot[data-day-index="${currentDayIndex}"]`).forEach(s => {
                                const hourIdx = parseInt(s.dataset.hourIndex);
                                if (hourIdx >= minHourIndex && hourIdx <= maxHourIndex) {
                                    if (isSelecting && !s.classList.contains('selected')) {
                                        s.classList.add('selected');
                                        s.classList.add('just-selected');
                                        setTimeout(() => {
                                            s.classList.remove('just-selected');
                                        }, 600);
                                    } else if (isDeselecting && s.classList.contains('selected')) {
                                        s.classList.remove('selected');
                                    }
                                }
                            });

                            updateSummary();
                        }
                    }
                });
            });

            // Arrêter la sélection lorsque le bouton de la souris est relâché
            document.addEventListener('mouseup', function() {
                isSelecting = false;
                isDeselecting = false;
                startDayIndex = null;
                startHourIndex = null;
            });

            // Événement pour le bouton "Tout effacer"
            clearBtn.addEventListener('click', function() {
                document.querySelectorAll('.time-slot.selected').forEach(slot => {
                    slot.classList.remove('selected');
                });
                updateSummary();
                showStatusMessage('Toutes les disponibilités ont été effacées');
            });



            // Événement pour le bouton "Enregistrer"
            saveBtn.addEventListener('click', function() {
                const selections = [];

                // Recueillir toutes les sélections
                days.forEach((day, i) => {
                    const daySlots = document.querySelectorAll(`.time-slot.selected[data-day-index="${i}"]`);
                    if (daySlots.length > 0) {
                        const dayHours = Array.from(daySlots).map(slot => slot.dataset.hour);
                        selections.push({
                            day,
                            hours: dayHours
                        });
                    }
                });

                // Créer un objet représentant les sélections
                const availabilityData = JSON.stringify(selections);

                // Envoi des données au serveur via AJAX
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '../backend/save_availability.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onload = function() {
                    if (this.status === 200) {
                        const response = JSON.parse(this.responseText);
                        if (response.success) {
                            showStatusMessage('Vos disponibilités ont été enregistrées avec succès !');
                            // Redirection vers la page de visualisation après 1.5 secondes

                        } else {
                            showStatusMessage('Erreur: ' + response.message, 'error');
                        }
                    } else {
                        showStatusMessage('Erreur de connexion au serveur', 'error');
                    }
                };

                xhr.onerror = function() {
                    showStatusMessage('Erreur de connexion au serveur', 'error');
                };

                xhr.send('availabilities=' + encodeURIComponent(availabilityData) + '&user_id=<?php echo $userId; ?>');
            });

            // Fonction pour mettre à jour le récapitulatif
            function updateSummary() {
                summaryList.innerHTML = '';

                // Récupérer les sélections par jour
                days.forEach((day, dayIndex) => {
                    const slots = document.querySelectorAll(`.time-slot.selected[data-day-index="${dayIndex}"]`);
                    if (slots.length > 0) {
                        // Regrouper les créneaux horaires consécutifs
                        const hourIndices = Array.from(slots).map(slot => parseInt(slot.dataset.hourIndex)).sort((a, b) => a - b);

                        let ranges = [];
                        let start = hourIndices[0];
                        let end = hourIndices[0];

                        for (let i = 1; i < hourIndices.length; i++) {
                            if (hourIndices[i] === end + 1) {
                                end = hourIndices[i];
                            } else {
                                ranges.push({
                                    start,
                                    end
                                });
                                start = hourIndices[i];
                                end = hourIndices[i];
                            }
                        }
                        ranges.push({
                            start,
                            end
                        });

                        // Créer l'élément de récapitulatif
                        const summaryItem = document.createElement('div');
                        summaryItem.className = 'summary-item';

                        let rangeText = '';
                        ranges.forEach((range, i) => {
                            const startHour = hours[range.start];
                            const endHour = parseInt(hours[range.end].split(':')[0]) + 1 + ':00';
                            rangeText += `${startHour} - ${endHour}`;
                            if (i < ranges.length - 1) rangeText += ', ';
                        });

                        summaryItem.textContent = `${day}: ${rangeText}`;
                        summaryList.appendChild(summaryItem);
                    }
                });

                // Afficher un message si aucune disponibilité n'est sélectionnée
                if (summaryList.children.length === 0) {
                    const emptyMessage = document.createElement('div');
                    emptyMessage.textContent = 'Aucune disponibilité sélectionnée';
                    emptyMessage.style.color = 'var(--gray-500)';
                    summaryList.appendChild(emptyMessage);
                }
            }

            // Initialiser le récapitulatif
            updateSummary();
        });
    </script>
</body>

</html>