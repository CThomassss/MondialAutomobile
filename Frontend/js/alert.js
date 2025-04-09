document.addEventListener("DOMContentLoaded", () => {
    const logoutLinks = document.querySelectorAll(".logout-link");
    const alertContainer = document.createElement("div");

    // Structure de l'alerte
    alertContainer.classList.add("alert-container");
    alertContainer.innerHTML = `
        <div class="alert-box">
            <h2>Êtes-vous sûr de vouloir vous déconnecter ?</h2>
            <div class="alert-buttons">
                <button class="btn-confirm">Oui</button>
                <button class="btn-cancel">Non</button>
            </div>
        </div>
    `;
    document.body.appendChild(alertContainer);

    const showAlert = () => alertContainer.classList.add("show");
    const hideAlert = () => alertContainer.classList.remove("show");

    logoutLinks.forEach(link => {
        link.addEventListener("click", (e) => {
            e.preventDefault();
            showAlert();

            // Gestion des boutons
            const confirmButton = alertContainer.querySelector(".btn-confirm");
            const cancelButton = alertContainer.querySelector(".btn-cancel");

            confirmButton.onclick = () => {
                fetch('/MondialAutomobile/Backend/logout_handler.php', { method: 'POST' })
                    .then(() => window.location.reload());
            };

            cancelButton.onclick = hideAlert;
        });
    });
});
