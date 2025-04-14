document.addEventListener("DOMContentLoaded", () => {
    const openPopup = document.getElementById("openPopup");
    const closePopup = document.getElementById("closePopup");
    const popupForm = document.getElementById("popupForm");

    openPopup?.addEventListener("click", () => {
        popupForm.style.display = "flex";
    });

    closePopup?.addEventListener("click", () => {
        popupForm.style.display = "none";
    });
});
