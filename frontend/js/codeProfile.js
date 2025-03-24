//searchBar
function searchFunction() {
    let input = document.getElementById("searchBar").value.toLowerCase();
    let items = document.querySelectorAll("#searchResults li");

    items.forEach(item => {
        let text = item.textContent.toLowerCase();
        if (text.includes(input)) {
            item.style.display = "block"; // Affiche si le texte correspond
        } else {
            item.style.display = "none";  // Cache si pas de correspondance
        }
    });
}

// Système de gestion des tags (centres d'intérêts et compétences)

document.addEventListener("DOMContentLoaded", function () {
    // Récupération des tags existants depuis la session
    let userTags = {};
    try {
        // Si des tags sont disponibles dans un attribut data, on les utilise
        const tagsElement = document.getElementById('userTagsData');
        if (tagsElement) {
            userTags = JSON.parse(tagsElement.dataset.tags || '{}');
        }
    } catch (e) {
        console.error("Erreur lors du parsing des tags:", e);
        userTags = {};
    }

    // Configuration du système de tags pour les centres d'intérêt
    initTagSystem({
        containerSelector: "#interestsContainer",
        searchSelector: "#interestSearch",
        addBtnSelector: "#addInterestBtn",
        menuSelector: "#interestMenu",
        listSelector: "#interestList",
        availableItems: ["Musique", "Sport", "Voyages", "Lecture", "Cinéma", "Cuisine", 
                         "Art", "Photographie", "Technologie", "Nature", "Jardinage", 
                         "Danse", "Théâtre", "Bénévolat", "Méditation", "Mode"],
        activeItems: userTags.interests || [],
        tagClass: "tag interest-tag",
        tagType: "interests"
    });

    // Configuration du système de tags pour les compétences
    initTagSystem({
        containerSelector: "#skillsContainer",
        searchSelector: "#skillSearch",
        addBtnSelector: "#addSkillBtn",
        menuSelector: "#skillMenu",
        listSelector: "#skillList",
        availableItems: ["HTML", "CSS", "JavaScript", "Python", "PHP", "SQL", "React", 
                         "Angular", "Vue", "Node.js", "Ruby", "Java", "C#", ".NET", 
                         "WordPress", "Photoshop", "Illustrator", "Communication", "Gestion de projet"],
        activeItems: userTags.skills || [],
        tagClass: "tag skill-tag",
        tagType: "skills"
    });
});

// Fonction pour envoyer les tags au serveur
function saveTags(type, tags) {
    return fetch('../backend/update_tags.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            type: type,
            tags: tags
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Affichage d'une notification de succès
            showNotification("Tags mis à jour avec succès", "success");
            return true;
        } else {
            console.error("Erreur lors de la sauvegarde:", data.message);
            showNotification("Erreur: " + data.message, "error");
            return false;
        }
    })
    .catch(error => {
        console.error("Erreur:", error);
        showNotification("Erreur de connexion", "error");
        return false;
    });
}

// Affichage d'une notification
function showNotification(message, type = "info") {
    // Création de l'élément de notification
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    // Styles pour la notification
    notification.style.position = 'fixed';
    notification.style.bottom = '20px';
    notification.style.right = '20px';
    notification.style.padding = '12px 20px';
    notification.style.borderRadius = '6px';
    notification.style.zIndex = '1000';
    notification.style.fontSize = '14px';
    notification.style.fontWeight = '500';
    notification.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
    
    if (type === 'success') {
        notification.style.backgroundColor = '#d1fae5';
        notification.style.color = '#065f46';
        notification.style.border = '1px solid #a7f3d0';
    } else if (type === 'error') {
        notification.style.backgroundColor = '#fee2e2';
        notification.style.color = '#991b1b';
        notification.style.border = '1px solid #fecaca';
    } else {
        notification.style.backgroundColor = '#e0f2fe';
        notification.style.color = '#075985';
        notification.style.border = '1px solid #bae6fd';
    }
    
    // Ajout au DOM
    document.body.appendChild(notification);
    
    // Suppression après 3 secondes
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.5s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 500);
    }, 3000);
}

// Fonction réutilisable pour initialiser un système de tags
function initTagSystem(config) {
    const container = document.querySelector(config.containerSelector);
    const searchInput = document.querySelector(config.searchSelector);
    const addBtn = document.querySelector(config.addBtnSelector);
    const menu = document.querySelector(config.menuSelector);
    const list = document.querySelector(config.listSelector);
    let availableItems = [...config.availableItems];
    let activeItems = [...config.activeItems];
    
    // Filtrer les éléments disponibles pour exclure ceux déjà actifs
    availableItems = availableItems.filter(item => !activeItems.includes(item));

    // Rendu des tags actifs
    function renderTags() {
        container.innerHTML = "";
        activeItems.forEach(item => {
            const tag = document.createElement("div");
            tag.className = config.tagClass;
            tag.innerHTML = `
                ${item}
                <svg class="remove-tag" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            `;
            tag.querySelector(".remove-tag").addEventListener("click", () => removeTag(item));
            container.appendChild(tag);
        });
    }

    // Supprimer un tag
    function removeTag(item) {
        activeItems = activeItems.filter(i => i !== item);
        availableItems.push(item);
        availableItems.sort();
        renderTags();
        saveTags(config.tagType, activeItems);
    }

    // Ajouter un tag
    function addTag(item) {
        if (!activeItems.includes(item) && item.trim() !== "") {
            activeItems.push(item);
            availableItems = availableItems.filter(i => i !== item);
            renderTags();
            searchInput.value = "";
            renderFilteredList("");
            saveTags(config.tagType, activeItems);
        }
    }

    // Filtrer et afficher la liste des éléments disponibles
    function renderFilteredList(filter = "") {
        list.innerHTML = "";
        const filteredItems = availableItems.filter(item => 
            item.toLowerCase().includes(filter.toLowerCase())
        );
        
        if (filteredItems.length === 0) {
            const li = document.createElement("li");
            li.textContent = "Aucun résultat trouvé";
            li.style.fontStyle = "italic";
            li.style.color = "#888";
            list.appendChild(li);
        } else {
            filteredItems.forEach(item => {
                const li = document.createElement("li");
                li.textContent = item;
                li.addEventListener("click", () => {
                    addTag(item);
                    menu.classList.add("hidden");
                });
                list.appendChild(li);
            });
        }
    }

    // Initialisation des événements
    searchInput.addEventListener("focus", () => {
        menu.classList.remove("hidden");
        renderFilteredList(searchInput.value);
    });

    searchInput.addEventListener("input", () => {
        renderFilteredList(searchInput.value);
    });

    addBtn.addEventListener("click", () => {
        if (menu.classList.contains("hidden")) {
            menu.classList.remove("hidden");
            renderFilteredList(searchInput.value);
            searchInput.focus();
        } else {
            menu.classList.add("hidden");
        }
    });

    // Gérer les clics hors du menu pour le fermer
    document.addEventListener("click", (event) => {
        if (!event.target.closest(config.menuSelector) && 
            !event.target.closest(config.searchSelector) && 
            !event.target.closest(config.addBtnSelector)) {
            menu.classList.add("hidden");
        }
    });

    // Permettre l'ajout de tags personnalisés avec la touche Entrée
    searchInput.addEventListener("keypress", (event) => {
        if (event.key === "Enter" && searchInput.value.trim() !== "") {
            const customTag = searchInput.value.trim();
            
            // Vérifier si le tag existe déjà dans les disponibles
            if (availableItems.includes(customTag)) {
                addTag(customTag);
            } else {
                // Ajouter un nouveau tag personnalisé
                activeItems.push(customTag);
                renderTags();
                searchInput.value = "";
                saveTags(config.tagType, activeItems);
            }
            
            menu.classList.add("hidden");
        }
    });

    // Affichage initial
    renderTags();
    renderFilteredList();
}

// Calendar

document.addEventListener("DOMContentLoaded", function () {
    let calendarEl = document.getElementById("calendar");

    if (calendarEl) {
        let calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: "dayGridMonth",
            locale: "fr",
            selectable: true,
            editable: true,
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,timeGridWeek,timeGridDay"
            },
            events: [
                {
                    title: "Réunion",
                    start: "2025-03-10T10:00:00",
                    end: "2025-03-10T12:00:00"
                },
                {
                    title: "Disponibilité",
                    start: "2025-03-12",
                    allDay: true
                }
            ],
            dateClick: function (info) {
                let eventTitle = prompt("Entrez un titre pour cette disponibilité :");
                if (eventTitle) {
                    calendar.addEvent({
                        title: eventTitle,
                        start: info.dateStr,
                        allDay: true
                    });
                }
            },
            eventClick: function (info) {
                if (confirm("Voulez-vous supprimer cet événement ?")) {
                    info.event.remove();
                }
            }
        });

        calendar.render();
        setTimeout(() => {
            let todayCell = document.querySelector(".fc-day-today");
            if (todayCell) {
                todayCell.scrollIntoView({ behavior: "smooth", block: "center" });
            }
        }, 500);
    } else {
        console.error("L'élément #calendar est introuvable.");
    }
});

// Modifier le profil

document.addEventListener("DOMContentLoaded", function () {
    const editProfileBtn = document.getElementById("editProfileBtn");
    const editProfileForm = document.getElementById("editProfileForm");
    const profileForm = document.getElementById("profileForm");
    const cancelEditBtn = document.getElementById("cancelEdit");

    // Afficher le formulaire de modification
    editProfileBtn.addEventListener("click", function () {
        editProfileForm.style.display = "block";
        editProfileBtn.style.display = "none";
    });

    // Annuler et cacher le formulaire
    cancelEditBtn.addEventListener("click", function () {
        editProfileForm.style.display = "none";
        editProfileBtn.style.display = "block";
    });

    // Gestion de la soumission du formulaire
    profileForm.addEventListener("submit", function (event) {
        event.preventDefault();

        // Récupérer les valeurs des champs
        const name = document.getElementById("name").value;
        const location = document.getElementById("location").value;
        const bio = document.getElementById("bio").value;
        const profilePicInput = document.getElementById("profilePicInput").files[0];

        // Mettre à jour le profil
        document.getElementById("profileName").innerText = name;
        document.getElementById("profileLocation").innerText = location;
        document.getElementById("profileBio").innerText = bio;

        // Si une nouvelle image est ajoutée, la mettre à jour
        if (profilePicInput) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById("profilePic").src = e.target.result;
            };
            reader.readAsDataURL(profilePicInput);
        }

        // Cacher le formulaire après la modification
        editProfileForm.style.display = "none";
        editProfileBtn.style.display = "block";
    });
});
document.addEventListener("DOMContentLoaded", function () {
    const profilePicInput = document.getElementById("profilePicInput");
    const profilePic = document.getElementById("profilePic");

    document.getElementById("profileForm").addEventListener("submit", function (event) {
        event.preventDefault();

        const file = profilePicInput.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                profilePic.src = e.target.result;
                
                // S'assurer que la photo reste bien ronde
                profilePic.style.width = "100%";
                profilePic.style.height = "100%";
                profilePic.style.objectFit = "cover";
            };
            reader.readAsDataURL(file);
        }
    });
});