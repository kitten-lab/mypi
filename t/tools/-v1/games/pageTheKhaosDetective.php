<style>

#AppContainer {
  display: flex;
  height:81vh; 
  width: 100%; 
  justify-content: center; 
  align-items: center; 
}

#GameScreen {
  flex: 1;
  text-align: center;
}

#IntroScreen {
  flex: 1;
  text-align: center;
}

.gameBtn {
  flex: 1;
  position: relative;
  margin: 10px;
  padding: 10px;
  text-align: center;
  justify-content: center; 
  width: 256px;
}

h1.title {
  position: absolute;
  display: flex;
  width: 100%;
}

</style>



<div id="AppContainer">
  <div id="GameScreen">
  </div>
</div>


<script>




const GameScreen = document.getElementById("GameScreen")
const GameTitle = "The Khaos Detective"

GameScreen.innerHTML = "<h1 id='title'>" + GameTitle + "</h1><div>CHAPTER 1: <br /> Out of Tune, Out of Time</div><div><button onclick='LoadROM()'>Load ROM</button>";


function LoadROM(){
  GameScreen.innerHTML = "<div id='IntroScreen'></div>";
  
  const IntroScreen = document.getElementById("IntroScreen")
  IntroScreen.innerHTML = "<h1 id='IntroTitle'>" + GameTitle + "</h1>";
  IntroScreen.innerHTML += "<button class='gameBtn'>Load Save File</button>";
  IntroScreen.innerHTML += "<button class='gameBtn' onclick='Level1()'>New Game</button>";
  IntroScreen.innerHTML += "<button class='gameBtn'>Rules & Credits</button>";
  IntroScreen.innerHTML += "</div>";
}

// ghostWRITER function
function typeWriter(text, i = 0, callback) {
  if (i < text.length) {
    StoryText.innerHTML += text.charAt(i);
    i++;
    setTimeout(() => typeWriter(text, i, callback), 30); 
  } else if (callback) {
    callback();
  }
}

function Level1(){

    GameScreen.innerHTML = "<div id='StoryText'></div><div id='ChoicesContainer'></div>";
    const StoryText = document.getElementById("StoryText");
    const ChoicesContainer = document.getElementById("ChoicesContainer");

    ChoicesContainer.innerHTML = "";
    StoryText.innerHTML = "";

  const stories = [
    {
        title: "The Lost Cathedral",
        client: "Jack Weak",
        clues: [
          "clue 1",
          "clue 2"
        ]
      },{
        title: "The Half Way",
        client: "Some Mary",
        intro: "She walks into the room. There isn't much to see. Gray eyes, gray skin, washed out. Common. They all look like this now.",
        clues: [
          "clue1", 
          "clues2"
        ]
      }
  ];
  const CurrentStory = null;

  const GameSetting = [
    CurrentStory = The_Half_Way,
  ]

    StoryText.innerHTML = typeWriter(stories[GameSetting.CurrentStory]intro);


function showChoices() {
    ChoicesContainer.innerHTML = `
        <button onclick="typeWriter('You walk to the door.')">Open Door</button>
        <button onclick="typeWriter('You sit in the corner.')">Sit</button>
    `;
}

}

//test jump 
</script>