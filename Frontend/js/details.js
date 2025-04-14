let currentIndex = 0;

function updateCarousel() {
    const images = document.querySelectorAll(".carousel-image");
    images.forEach((img, index) => {
        img.classList.toggle("active", index === currentIndex);
    });
}

function nextImage() {
    const images = document.querySelectorAll(".carousel-image");
    currentIndex = (currentIndex + 1) % images.length;
    updateCarousel();
}

function prevImage() {
    const images = document.querySelectorAll(".carousel-image");
    currentIndex = (currentIndex - 1 + images.length) % images.length;
    updateCarousel();
}

document.addEventListener("DOMContentLoaded", () => {
    updateCarousel();
});
