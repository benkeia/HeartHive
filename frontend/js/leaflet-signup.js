document.addEventListener("DOMContentLoaded", () => {
    // Configuration de la carte
    const customIcon = L.icon({
        iconUrl: 'assets/icons/target.png',
        iconSize: [20, 20],
        iconAnchor: [10, 10],
        popupAnchor: [0, -40]
    });

    const map = L.map("map", {
        scrollWheelZoom: false,
        zoomControl: false
    }).setView([46.603354, 1.888334], 6);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "© OpenStreetMap contributors",
    }).addTo(map);

    // Éléments DOM
    const citySearch = document.getElementById("city-search");
    const suggestions = document.getElementById("suggestions");
    const validationIcon = document.getElementById("validation-icon");
    const radiusRange = document.getElementById("radius-range");
    const nbkm = document.getElementById("nb-km");

    // Variables d'état
    let marker, circle, cityGeoJSON, currentCityCode;
    const radiusValues = [0, 1, 5, 10, 20, 30, 50, 100, 200];

    // Gestion de l'autocomplétion
    citySearch.addEventListener("input", async () => {
        const searchTerm = citySearch.value.trim();
        
        if (searchTerm.length > 2) {
            try {
                const response = await fetch(`https://geo.api.gouv.fr/communes?nom=${searchTerm}&fields=nom,centre,code&boost=population&limit=5`);
                const data = await response.json();
                showSuggestions(data);
            } catch (error) {
                console.error("Erreur lors de la récupération des données:", error);
                hideSuggestions();
            }
        } else {
            hideSuggestions();
        }
    });

    // Affichage des suggestions
    function showSuggestions(communes) {
        suggestions.innerHTML = communes.map(commune => `
            <div class="suggestion" data-code="${commune.code}" data-lat="${commune.centre.coordinates[1]}" data-lon="${commune.centre.coordinates[0]}">
                ${commune.nom.trim()} <!-- Utilisation de .trim() pour supprimer les espaces -->
            </div>
        `).join('');

        suggestions.classList.add('active');
        
        // Gestion des clics sur les suggestions
        document.querySelectorAll('.suggestion').forEach(item => {
            item.addEventListener('click', () => {
                citySearch.value = item.textContent.trim(); // Utilisation de .trim() ici aussi
                validationIcon.textContent = "✅";
                validationIcon.style.color = "green";
                setCityLocation({
                    code: item.dataset.code,
                    nom: item.textContent.trim(), // Utilisation de .trim() ici aussi
                    centre: {
                        coordinates: [parseFloat(item.dataset.lon), parseFloat(item.dataset.lat)]
                    }
                });
                hideSuggestions();
            });
        });
    }

    // Masquage des suggestions
    function hideSuggestions() {
        suggestions.classList.remove('active');
        suggestions.innerHTML = "";
    }

    // Positionnement sur la carte
    function setCityLocation(commune) {
        const { coordinates } = commune.centre;
        
        // Nettoyage des éléments existants
        if (marker) marker.remove();
        if (circle) circle.remove();
        if (cityGeoJSON) cityGeoJSON.remove();

        // Nouveau marqueur
        marker = L.marker([coordinates[1], coordinates[0]], {
            icon: customIcon
        }).addTo(map);

        // Centrage de la carte
        map.setView(marker.getLatLng(), 12, { animate: false });

        // Mise à jour de l'état
        currentCityCode = commune.code;
        updateCircle();
    }

    // Mise à jour du cercle/contour
    async function updateCircle() {
        const radius = radiusValues[radiusRange.value] * 1000;
        
        if (circle) circle.remove();
        if (cityGeoJSON) cityGeoJSON.remove();

        if (radius === 0 && currentCityCode) {
            try {
                const response = await fetch(`https://geo.api.gouv.fr/communes/${currentCityCode}?fields=contour`);
                const data = await response.json();
                cityGeoJSON = L.geoJSON(data.contour, {
                    color: "#0C5193"
                }).addTo(map);
                map.fitBounds(cityGeoJSON.getBounds());
            } catch (error) {
                console.error("Erreur lors du chargement du contour de la ville:", error);
            }
        } else if (marker) {
            circle = L.circle(marker.getLatLng(), {
                radius,
                color: "#0C5193",
            }).addTo(map);
            map.fitBounds(circle.getBounds());
        }
    }

    // Gestion du range
    radiusRange.addEventListener("input", () => {
        const index = parseInt(radiusRange.value, 10);
        nbkm.textContent = `${radiusValues[index]} km`;
        updateCircle();
    });

    // Gestion du redimensionnement de la carte
    const handleMapResize = () => {
        setTimeout(() => map.invalidateSize(), 300);
    };

    window.addEventListener("load", handleMapResize);
    document.querySelectorAll(".btn-next, .btn-prev").forEach(btn => {
        btn.addEventListener("click", handleMapResize);
    });

    // Fermeture des suggestions quand on clique ailleurs
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#city-search') && !e.target.closest('#suggestions')) {
            hideSuggestions();
        }
    });
});


document.addEventListener('DOMContentLoaded', () => {
    const form = $('#multiStepForm').parsley();
    let currentStep = 0;
    const steps = document.querySelectorAll('.form-step');
    const progress = document.getElementById('progress');
    const progressSteps = document.querySelectorAll('.progress-step');
    
    function updateUI() {
        steps.forEach((step, index) => {
            step.classList.toggle('form-step-active', index === currentStep);
        });
  
        const progressPercent = (currentStep / (steps.length - 1)) * 100;
        progress.style.width = `${progressPercent}%`;
  
        progressSteps.forEach((step, index) => {
            step.classList.toggle('progress-step-active', index <= currentStep);
        });
    }
  
    function handleNavigation(direction) {
        if (direction === 1) {
            form.validate({group: `step${currentStep + 1}`, force: true});
            if (!form.isValid({group: `step${currentStep + 1}`})) {
                return;
            }
        }
  
        currentStep = Math.max(0, Math.min(currentStep + direction, steps.length - 1));
        updateUI();
    }
  
    document.querySelectorAll('.btn-next').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            handleNavigation(1);
        });
    });
  
    document.querySelectorAll('.btn-prev').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            handleNavigation(-1);
        });
    });
  
    // Validation de la date de naissance
    window.Parsley.addValidator('age', {
        validateString: function(value) {
            const birthDate = new Date(value);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            return age >= 18;
        },
        messages: {
            fr: 'Vous devez avoir au moins 18 ans.'
        }
    });
  
    $('#birth').attr('data-parsley-age', '');
  
    // Configurer la date min et max pour le champ de date de naissance
    const birthInput = document.getElementById('birth');
    const today = new Date();
    const minDate = new Date(today.getFullYear() - 100, today.getMonth(), today.getDate());
    const maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
  
    birthInput.min = minDate.toISOString().split('T')[0];
    birthInput.max = maxDate.toISOString().split('T')[0];
  
    updateUI();

    // Afficher l'image sélectionnée
    const profilePicInput = document.getElementById('profile-pic');
    const profilePicPreview = document.getElementById('profile-pic-preview');

    profilePicInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePicPreview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // Vérifier que l'image est bien uploadée
    form.on('form:submit', function() {
        if (!profilePicInput.files.length) {
            alert('Veuillez télécharger une photo de profil.');
            return false; // Empêche la soumission du formulaire
        }
        return true; // Permet la soumission du formulaire
    });
});