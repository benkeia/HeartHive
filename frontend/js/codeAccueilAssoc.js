function updateStatus(button, status) {
    let row = button.closest("tr");
    let statusCell = row.querySelector(".status");
    let acceptButton = row.querySelector(".btn-accept");
    let rejectButton = row.querySelector(".btn-reject");

    // Vérifier si le bouton est déjà actif
    let isSelected = button.classList.contains("btn-selected-accept") || button.classList.contains("btn-selected-reject");

    // Si le bouton est déjà actif, tout réinitialiser
    if (isSelected) {
        statusCell.textContent = "En attente";
        statusCell.className = "status pending";
        acceptButton.classList.remove("btn-selected-accept");
        rejectButton.classList.remove("btn-selected-reject");
        acceptButton.style.display = "inline-block";
        rejectButton.style.display = "inline-block";
        return;
    }

    // Réinitialiser les boutons
    acceptButton.classList.remove("btn-selected-accept");
    rejectButton.classList.remove("btn-selected-reject");
    acceptButton.style.display = "inline-block";
    rejectButton.style.display = "inline-block";

    // Mettre à jour en fonction du choix
    if (status === "accepted") {
        statusCell.textContent = "Accepté";
        statusCell.className = "status accepted";
        acceptButton.classList.add("btn-selected-accept");
        rejectButton.style.display = "none";
    } else {
        statusCell.textContent = "Refusé";
        statusCell.className = "status rejected";
        rejectButton.classList.add("btn-selected-reject");
        acceptButton.style.display = "none";
    }
}