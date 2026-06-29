const KDC001 = {
    vencode: "KCD001",
    provider: "Chester's Imports",
    toy: "My PI: Detective",
    funtitle: "Keys and Chords Noted",
    info: "",
    funtags: [ "Game", "ROM", "Detective", "Randomizer", "Story Reader" ]
};




function ToggleKCD001(){
    if (loaded == true) {
        CloseROM();
        loaded = false;
    } else {
        LoadKCD001();
    }
}


function LoadKCD001(){
   loaded = true;
   const ROM_CONTENT = document.getElementById("ROM_SCREEN");

    ROM_CONTENT.innerHTML += `
  <div id="ROM_CONTENT">
  <div id="grabby">GRABBY</div>
  <div id="SiloROM_BaseBox" class="SiloROM_BaseBox">
    <romHeader id="romHeader">
    </romHeader>
    <romBar class="ROM_BAR">
        <div id="AppControls" class="AppControls">
        </div>
    </romBar>
    <romMainScreen id="GameScreen"> 
        <div id='CardDraw'>
        </div>
    </romMainScreen>
    <romFooter>
    </romFooter>
    </div>
    </div>
    `;

    ROM_Header = document.getElementById("romHeader");
    AppControls = document.getElementById("AppControls");

GameScreen.innerHTML = "<h1 id='title'>" + KDC001.toy + "</h1><div>CHAPTER 1: <br /> Out of Tune, Out of Time</div>";
  
  ROM_Header.innerHTML = "<h1 id='IntroTitle'>" + KDC001.toy + "</h1>";
  AppControls.innerHTML += "<button class='gameBtn'>Load Save File</button>";
  AppControls.innerHTML += "<button class='gameBtn' onclick='Level1()'>New Game</button>";
  AppControls.innerHTML += "<button class='gameBtn'>Rules & Credits</button>";
  AppControls.innerHTML += "</div>";

  

const handle = document.getElementById("grabby");
const draggable = document.getElementById("ROM_CONTENT");

let offsetX, offsetY;

handle.addEventListener('mousedown', (e) => {
    // Calculate the offset position
    offsetX = e.clientX - draggable.offsetLeft;
    offsetY = e.clientY - draggable.offsetTop;

    // Add event listeners to the document for mousemove and mouseup
    document.addEventListener('mousemove', mouseMoveHandler);
    document.addEventListener('mouseup', mouseUpHandler);
});

function mouseMoveHandler(e) {
    // Update the position of the draggable element
    draggable.style.left = `${e.clientX - offsetX}px`;
    draggable.style.top = `${e.clientY - offsetY}px`;
    draggable.style.position = 'absolute'; // Set position to absolute
}

function mouseUpHandler() {
    // Remove event listeners when mouse is released
    document.removeEventListener('mousemove', mouseMoveHandler);
    document.removeEventListener('mouseup', mouseUpHandler);
}

}


function CloseROM(){

  ROM_CONTENT.innerHTML = "";


}
