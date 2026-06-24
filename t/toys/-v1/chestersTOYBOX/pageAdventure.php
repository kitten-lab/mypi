<!-- HTML Structure -->
<div id="game-container">
    <p id="story-text"></p>
    <div id="choices">
        <button onclick="startGame()">Start</button>
    </div>
</div>

<script>
// JavaScript Logic
const storyText = document.getElementById("story-text");
const choicesContainer = document.getElementById("choices");

// Typewriter function
function typeWriter(text, i = 0, callback) {
    if (i < text.length) {
        storyText.innerHTML += text.charAt(i);
        i++;
        setTimeout(() => typeWriter(text, i, callback), 30); // Speed: 30ms per char
    } else if (callback) {
        callback();
    }
}

// Adventure Logic
function startGame() {
    choicesContainer.innerHTML = "";
    storyText.innerHTML = "";
    typeWriter("You are in a dark room. What do you do?", 0, showChoices);
}

function showChoices() {
    choicesContainer.innerHTML = `
        <button onclick="typeWriter('You walk to the door.')">Open Door</button>
        <button onclick="typeWriter('You sit in the corner.')">Sit</button>
    `;
}
</script>
