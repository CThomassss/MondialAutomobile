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
        const userMessage = chatbotInput.value.trim();
        if (!userMessage) return;

        // Display user message
        const userMessageElement = document.createElement("div");
        userMessageElement.classList.add("message", "user");
        userMessageElement.textContent = userMessage;
        chatbotMessages.appendChild(userMessageElement);

        // Scroll to the bottom
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

        // Send message to chatbot
        const response = await fetch(`/MondialAutomobile/chatbot/chatbot.php?question=${encodeURIComponent(userMessage)}`);
        const botMessage = await response.text();

        // Display bot response
        const botMessageElement = document.createElement("div");
        botMessageElement.classList.add("message", "bot");
        botMessageElement.textContent = botMessage;
        chatbotMessages.appendChild(botMessageElement);

        // Scroll to the bottom
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

        // Clear input
        chatbotInput.value = "";
    });
});
