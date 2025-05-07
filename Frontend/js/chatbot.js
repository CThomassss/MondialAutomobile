document.addEventListener("DOMContentLoaded", () => {
    const chatbotContainer = document.getElementById("chatbot");
    const openChatbotButton = document.getElementById("openChatbot");
    const closeChatbotButton = document.getElementById("closeChatbot");
    const chatbotForm = document.getElementById("chatbotForm");
    const chatbotInput = document.getElementById("chatbotInput");
    const chatbotMessages = document.getElementById("chatbotMessages");

    // Open chatbot
    openChatbotButton.addEventListener("click", () => {
        chatbotContainer.style.display = "flex";
        openChatbotButton.style.display = "none";
    });

    // Close chatbot
    closeChatbotButton.addEventListener("click", () => {
        chatbotContainer.style.display = "none";
        openChatbotButton.style.display = "block";
    });

    // Handle form submission
    chatbotForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const input = document.getElementById('chatbotInput');
        const question = input.value.trim();
        if (!question) return;

        const responseDiv = document.getElementById('chatbotMessages');
        responseDiv.innerHTML += `<div class="message user">${question}</div>`;

        const response = await fetch(`/MondialAutomobile/api.php?endpoint=chatbot&question=${encodeURIComponent(question)}`);
        const data = await response.json();

        responseDiv.innerHTML += `<div class="message bot">${data.response}</div>`;
        input.value = '';
    });
});
