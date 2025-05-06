document.addEventListener("DOMContentLoaded", () => {
    // Add the fade-in class when the page loads
    document.body.classList.add("fade-in");

    // Add a click event listener to all links
    document.querySelectorAll("a").forEach(link => {
        link.addEventListener("click", (event) => {
            const href = link.getAttribute("href");
            // Exclude logout links from the transition
            if (link.classList.contains("logout-link") || !href || href.startsWith("#") || link.hasAttribute("target")) {
                return;
            }
            event.preventDefault();
            document.body.classList.remove("fade-in");
            document.body.style.transition = "opacity 0.8s ease-in-out"; // Longer and smoother transition
            document.body.style.opacity = "0";
            setTimeout(() => {
                window.location.href = href;
            }, 400); // Match the transition duration
        });
    });
});
