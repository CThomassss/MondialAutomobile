document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");
    const inputs = form.querySelectorAll("input, textarea");

    // Validation du formulaire
    form.addEventListener("submit", (e) => {
        let isValid = true;
        inputs.forEach((input) => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add("error");
            } else {
                input.classList.remove("error");
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert("Veuillez remplir tous les champs.");
        }
    });

    // Animations des champs
    inputs.forEach((input) => {
        input.addEventListener("focus", () => {
            input.style.boxShadow = "0 0 5px rgba(175, 46, 52, 0.5)";
        });

        input.addEventListener("blur", () => {
            input.style.boxShadow = "none";
        });
    });
});
