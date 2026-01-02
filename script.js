document.addEventListener("DOMContentLoaded", () => {
  const input = document.getElementById("jammy-input");
  const messages = document.getElementById("jammy-messages");

  function addMessage(text, className) {
    const msg = document.createElement("div");
    msg.classList.add("message", className);
    msg.textContent = text;
    messages.appendChild(msg);
    messages.scrollTop = messages.scrollHeight;
  }

  input.addEventListener("keypress", function (e) {
    if (e.key === "Enter" && input.value.trim() !== "") {
      const userMessage = input.value.trim();
      addMessage(userMessage, "user-message");
      input.value = "";

      fetch(`backend.php?message=${encodeURIComponent(userMessage)}`)
        .then(res => res.json())
        .then(data => addMessage(data.reply, "bot-message"))
        .catch(err => addMessage("⚠️ Server error. Try again.", "bot-message"));
    }
  });
});
