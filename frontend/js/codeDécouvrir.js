
// Code pour la page découvrir

document.addEventListener("DOMContentLoaded", function () {
    // Gérer la mise à jour du curseur de distance
    const distanceInput = document.getElementById("distance");
    const distanceValue = document.getElementById("distanceValue");

    distanceInput.addEventListener("input", function () {
        distanceValue.textContent = this.value + "km";
    });

    // Sélectionner/Désélectionner les catégories
    document.querySelectorAll(".category-btn").forEach(button => {
        button.addEventListener("click", function () {
            this.classList.toggle("active");
        });
    });
});





