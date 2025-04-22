document.addEventListener("DOMContentLoaded", () => {
    const links = document.querySelectorAll("a[href]");
    links.forEach(link => {
        link.addEventListener("click", (e) => {
            const target = e.currentTarget.getAttribute("href");
            if (target && !target.startsWith("#") && !target.startsWith("javascript")) {
                e.preventDefault();
                document.body.style.animation = "fadeOut 0.5s ease-in-out forwards";
                setTimeout(() => {
                    window.location.href = target;
                }, 500);
            }
        });
    });
});
