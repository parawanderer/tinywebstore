// only submit form when valid 
const messageForm = document.getElementById("messageForm");
const sendMessageButton = document.getElementById("sendMessageButton");
const contentInput = document.getElementById("contentInput");

// only allow to message with content
if (!contentInput.value?.trim()) {
    sendMessageButton.disabled = true;
} else {
    sendMessageButton.disabled = false;
}
contentInput.addEventListener("input", function(event) {
    const input = contentInput.value?.trim();

    if (!input) {
        sendMessageButton.disabled = true;
    } else {
        sendMessageButton.disabled = false;
    }
});

// last messages should be visible
const messageList = document.getElementById("messageList");
messageList.scroll({
    top: messageList.clientHeight,
    behavior: 'auto'
});
