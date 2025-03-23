
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



//Centres d'interets

document.addEventListener("DOMContentLoaded", function () {
    const interestsContainer = document.getElementById("interestsContainer");
    const addInterestBtn = document.getElementById("addInterestBtn");
    const interestMenu = document.getElementById("interestMenu");
    const interestList = document.getElementById("interestList");

    // Liste des centres d'intérêts disponibles
    let availableInterests = ["Musique", "Sport", "Voyages", "Lecture", "Cinéma", "Cuisine"];
    let activeInterests = [];

    // Fonction pour afficher les centres d'intérêts actifs
    function renderInterests() {
        interestsContainer.innerHTML = "";
        activeInterests.forEach((interest) => {
            let btn = document.createElement("button");
            btn.textContent = interest;
            btn.classList.add("interest-btn");
            btn.onclick = () => removeInterest(interest);
            interestsContainer.appendChild(btn);
        });
    }

    // Supprimer un centre d'intérêt
    function removeInterest(interest) {
        activeInterests = activeInterests.filter((i) => i !== interest);
        renderInterests();
    }

    // Ouvrir/Fermer le menu des centres d'intérêt
    function toggleInterestMenu() {
        interestMenu.style.display = interestMenu.style.display === "block" ? "none" : "block";
        interestList.innerHTML = "";

        availableInterests.forEach((interest) => {
            if (!activeInterests.includes(interest)) {
                let li = document.createElement("li");
                let checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.value = interest;

                checkbox.onchange = function () {
                    if (this.checked) {
                        activeInterests.push(interest);
                        renderInterests();
                        interestMenu.style.display = "none"; // Ferme le menu après sélection
                    }
                };

                li.appendChild(checkbox);
                li.appendChild(document.createTextNode(interest));
                interestList.appendChild(li);
            }
        });
    }

    // Événement sur le bouton "+"
    addInterestBtn.addEventListener("click", toggleInterestMenu);

    // Affichage initial
    renderInterests();
});



// Compétences

document.addEventListener("DOMContentLoaded", function () {
    const skillsContainer = document.getElementById("skillsContainer");
    const addSkillBtn = document.getElementById("addSkillBtn");
    const skillMenu = document.getElementById("skillMenu");
    const skillList = document.getElementById("skillList");

    // Liste des compétences disponibles
    let availableSkills = ["HTML", "CSS", "JavaScript", "Python", "React", "SQL"];
    let activeSkills = [];

    // Fonction pour afficher les compétences actives
    function renderSkills() {
        skillsContainer.innerHTML = "";
        activeSkills.forEach((skill) => {
            let btn = document.createElement("button");
            btn.textContent = skill;
            btn.classList.add("interest-btn"); // Réutilisation du même style que les centres d’intérêts
            btn.onclick = () => removeSkill(skill);
            skillsContainer.appendChild(btn);
        });
    }

    // Supprimer une compétence
    function removeSkill(skill) {
        activeSkills = activeSkills.filter((s) => s !== skill);
        renderSkills();
    }

    // Ouvrir/Fermer le menu des compétences
    function toggleSkillMenu() {
        if (skillMenu.style.display === "block") {
            skillMenu.style.display = "none";
        } else {
            skillMenu.style.display = "block";

            // Positionner le menu sous le bouton "+"
            let btnRect = addSkillBtn.getBoundingClientRect();
            skillMenu.style.top = `${btnRect.bottom + window.scrollY + 5}px`;
            skillMenu.style.left = `${btnRect.left + window.scrollX}px`;
        }

        skillList.innerHTML = "";

        availableSkills.forEach((skill) => {
            if (!activeSkills.includes(skill)) {
                let li = document.createElement("li");
                let checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.value = skill;

                checkbox.onchange = function () {
                    if (this.checked) {
                        activeSkills.push(skill);
                        renderSkills();
                        skillMenu.style.display = "none"; // Ferme le menu après sélection
                    }
                };

                li.appendChild(checkbox);
                li.appendChild(document.createTextNode(skill));
                skillList.appendChild(li);
            }
        });
    }

    // Événement sur le bouton "+"
    addSkillBtn.addEventListener("click", toggleSkillMenu);

    // Affichage initial
    renderSkills();
});


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