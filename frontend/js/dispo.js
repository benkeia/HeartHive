document.addEventListener('DOMContentLoaded', function() {
    // Initialisation du composant de disponibilités
    console.log("Initialisation du composant de disponibilités");
    initAvailabilityComponent();
});

function initAvailabilityComponent() {
    // Vérifier que l'élément cible existe
    const availabilityPane = document.querySelector('#availability-pane');
    if (!availabilityPane) {
        console.log("Élément #availability-pane non trouvé");
        return;
    }
    
    console.log("Le composant #availability-pane a été trouvé");
    
    // Trouver tous les créneaux horaires
    const timeSlots = availabilityPane.querySelectorAll('.time-slot');
    if (timeSlots.length === 0) {
        console.log("Aucun élément .time-slot trouvé");
        return;
    }
    
    console.log(`${timeSlots.length} créneaux horaires trouvés`);

    // Variables pour gérer le drag and drop
    let isSelecting = false;
    let isDeselecting = false;
    let startDayIndex = null;
    let startHourIndex = null;

    // S'assurer que la variable globale 'availability' existe
    window.availability = [];

    // Gestionnaire d'événements pour les créneaux
    timeSlots.forEach(slot => {
        // Événement mousedown (début de sélection)
        slot.addEventListener('mousedown', function(e) {
            e.preventDefault(); // Empêcher la sélection de texte
            
            isSelecting = !this.classList.contains('selected');
            isDeselecting = this.classList.contains('selected');
            
            startDayIndex = parseInt(this.dataset.dayIndex);
            startHourIndex = parseInt(this.dataset.hourIndex);
            
            // Basculer l'état du créneau
            if (isSelecting) {
                this.classList.add('selected', 'bg-pink-100', 'border-pink-300');
            } else {
                this.classList.remove('selected', 'bg-pink-100', 'border-pink-300');
            }
            
            updateAvailabilitySummary();
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
                    availabilityPane.querySelectorAll(`.time-slot[data-day-index="${currentDayIndex}"]`).forEach(s => {
                        const hourIdx = parseInt(s.dataset.hourIndex);
                        if (hourIdx >= minHourIndex && hourIdx <= maxHourIndex) {
                            if (isSelecting && !s.classList.contains('selected')) {
                                s.classList.add('selected', 'bg-pink-100', 'border-pink-300');
                            } else if (isDeselecting && s.classList.contains('selected')) {
                                s.classList.remove('selected', 'bg-pink-100', 'border-pink-300');
                            }
                        }
                    });
                    
                    updateAvailabilitySummary();
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

    // Fonction pour mettre à jour le récapitulatif des disponibilités
    function updateAvailabilitySummary() {
        const summary = document.getElementById('availability-summary');
        if (!summary) {
            console.log("Élément #availability-summary non trouvé");
            return;
        }
        
        const selectedSlots = availabilityPane.querySelectorAll('.time-slot.selected');
        
        // Réinitialiser le récapitulatif et les données
        summary.innerHTML = '';
        window.availability = [];
        
        if (selectedSlots.length === 0) {
            summary.innerHTML = '<span class="text-sm text-gray-500">Aucune disponibilité sélectionnée</span>';
            return;
        }
        
        // Organiser les sélections par jour
        const daySelections = {};
        selectedSlots.forEach(slot => {
            const day = slot.dataset.day;
            const hour = slot.dataset.hour;
            
            if (!daySelections[day]) {
                daySelections[day] = { hours: [] };
            }
            
            // Éviter les doublons
            if (!daySelections[day].hours.includes(hour)) {
                daySelections[day].hours.push(hour);
            }
        });
        
        // Créer les éléments de récapitulatif
        for (const day in daySelections) {
            const dayItem = document.createElement('div');
            dayItem.className = 'px-3 py-1 bg-pink-50 text-pink-700 rounded-full text-xs border border-pink-200';
            dayItem.textContent = day;
            summary.appendChild(dayItem);
        }
        
        // Mettre à jour le tableau des disponibilités dans le même format que update_availability.php
        for (const day in daySelections) {
            window.availability.push({
                day: day,
                hours: daySelections[day].hours
            });
        }
        
        console.log("Disponibilités mises à jour:", window.availability);
    }
}