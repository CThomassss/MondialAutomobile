document.addEventListener("DOMContentLoaded", () => {
    // Animation des sections au survol
    const sections = document.querySelectorAll(".admin-section");
    sections.forEach((section) => {
        section.addEventListener("mouseenter", () => {
            section.style.transform = "translateY(-10px)";
            section.style.boxShadow = "0 12px 30px rgba(0, 0, 0, 0.2)";
        });

        section.addEventListener("mouseleave", () => {
            section.style.transform = "translateY(0)";
            section.style.boxShadow = "0 6px 20px rgba(0, 0, 0, 0.1)";
        });
    });

    // Animation des boutons au clic
    const buttons = document.querySelectorAll(".btn-edit, .btn-delete");
    buttons.forEach((button) => {
        button.addEventListener("click", (e) => {
            e.target.style.transform = "scale(0.95)";
            setTimeout(() => {
                e.target.style.transform = "scale(1)";
            }, 150);
        });
    });
});
