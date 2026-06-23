

// load draw types
drawTypes = [
  "A card from the center of the deck.",
  "A card flies out without hesitation.",
  "A pile flops on the table, with only one card face up.",
  "A card from the top of the deck.",
  "A card from the bottom of the deck."
];

window.DECK = [
    {
      deckname: "The 90s Internet",
      cards: [
        {
        cardname: "UNDER CONSTRUCTION",
        cardnumber: 0,
        cardmeaning: "Even if it never launched, the UNDER CONSTRUCTION sign means there is still a chance.",
        bardy: [
          "You sometimes think you never started, but that animated gif's telling a different story.",
          "Under construction is one step beyond Coming Soon. Keep going, doll.",
          "A construction site is still capable of blooming a rose from concrete. Yes, that is a Dark Tower reference.",
          "Rapid iteration sometimes feels like perpetual under construction, doesn't it?"
        ]
        },{
        cardname: "GUESTBOOK",
        cardnumber: 1,
        cardmeaning: "Someone left a message on your GUESTBOOK. \nYou maybe didn't see them, but they were here. Look, they left their mark.",
        bardy: [
          "Someone's thinking about you, and they definitely want you to know.",
          "You're getting some real traction now. Guestbook entries all day! Get it, girl!",
          "Sometimes, all you can see is the mark that someone was there. Its hard when you can't reach back, but also stop hitting refresh. There aren't any new posts yet.",
          "Sometimes, a guest is more than a guest."
        ]
      },{
        cardname: "WEBRING",
        cardnumber: 2,
        cardmeaning: "YOU'VE JOINED A WEB RING! You are now part of something, and you belong.",
        bardy: [
          "Oh, baby. Belonging sometimes happens just because you said it happens.",
          "You can belong before anyone says you can. The sign-up sheet permits entry.",
          "The best part about a webring is the niche. Suddenly, you can find more sight shaped like yours.",
          "The circle of life, amirite?"
        ]
      },{
        cardname: "NOTEPAD CODER",
        cardnumber: 3,
        cardmeaning: "If you didn't code it in Notepad, do you even really code? The Notepad Coder says pay attention to the craftsmanship, but beware of the ego.",
        bardy: [
          "Ego isn't inherently unwelcome here, but when you start shaming helper tools, are you building or gatekeeping?",
          "Listen, Notepad++. Color coding. Clarity of intent. Surely, you see the value in that.",
          "A coder should focus on what is built, not the pretentions of the tools used to build it.",
          "The tool used definitely does not define the quality of your craft. What matters is the pride of your build."
        ]
      },{
        cardname: "LOST PASSWORD",
        cardnumber: 4,
        cardmeaning: "You lost your password again. When you try to update it, you enter the same password you're already using. Why didn't it work? You may never know.",
        bardy: [
          "You keep trying to become someone you already were once. Maybe stop searching for the perfect login and write down what you still remember.",
          "Its not you. There are chip crumbles in the keyboard, making the keys stick. Get some canned air.",
          "Some doors are protecting old versions of you. You have to wonder, do you need to change it still, or merely remember?",
          "Yeah. You should just reset it this time. Typing it wrong is a sign of unalignment, I'd guess."
        ]
      },{
        cardname: "THE DIAL-UP SOUND",
        cardnumber: 5,
        cardmeaning: "The handshake of the open door. When you hear this sound, the world has opened up to you. Where will you go first?",
        bardy: [
          "You always were so excited to get online. A world explorer. Not much has changed, we're just on broadband now.",
          "Modern online lost the sound, but that doesn't mean the connection is gone.",
          "A deal is awaiting you. Just hhhhhrrr krrrr BEEP! BEEP! BEEP! skreeeeEEEEeehhh...",
          "Your search terms await you."
        ]
      }
      ]
    },{
      deckname: "Color Cards",
      cards: [
        {
        cardname: "RED",
        cardnumber: 0,
        cardmeaning: "Passion, Anger, Desire, Heat, Production, Fire, Transformation",
        bardy: [
          "There is a distinct need for action. Something is calling you forward.",
          "Close your eyes and look at the sun through your eyelids. That is the truth of red.",
          "What do you desire? Can you take action towards it?"
        ]
        },{
        cardname: "BLUE",
        cardnumber: 1,
        cardmeaning: "Emotion, Ease, Water, Movement, Fluidity, Depth, Imagination",
        bardy: [
          "There is always fluidity, even when things feel stiff. Strech something: your body, your thinking, your ideas.",
          "There is a depth of emotions inside that you hide from. Let the ocean out.",
          "Much like the sky and the sea have no true color, so does blue represent the imaginal. What is everywhere of yoou that you cannot see but is still percieved?"
        ]
        },{
        cardname: "GREEN",
        cardnumber: 1,
        cardmeaning: "Growth, Newness, Innocence, Pre-Development, Abundance, Production",
        bardy: [
          "Just confused."
        ]
        }
      ]
    }
];

// div collector

const MRA001 = {
    vencode: "MRA-001",
    provider: "Chester's Imports",
    toy: "Morana Arcana",
    style: "Julie",
    funtitle: "Fortuna Snacks",
    info: "A quick card draw with special BARDY DOLL interpretations!",
    funtags: [ "Game", "ROM", "Divination", "Randomizer", "Deck Reader" ]
};




function Draw1(){
let cardNo = 0;

  drawOne = CurrentDeck.cards[Math.floor(Math.random() * CurrentDeck.cards.length)]
  drawStyle = drawTypes[Math.floor(Math.random() * drawTypes.length)]
  bardyFeedback = drawOne.bardy[Math.floor(Math.random() * drawOne.bardy.length)]

  Your1CardReading = "<div id='" + cardNo++ + "'>" + drawStyle + " \n-" + drawOne.cardnumber + "-\n" + drawOne.cardname + "\n\n" + drawOne.cardmeaning + "\n\nBAR-B SAYS: " + bardyFeedback;
  console.log(Your1CardReading);
  CardDraw.innerHTML = Your1CardReading;
}

function ToggleMRA001(){
    if (loaded == true) {
        CloseROM();
        loaded = false;
    } else {
        LoadMRA001();
    }
}

let loaded = false;

// load the ROM
function LoadMRA001(){
    loaded = true;

    // loading DECKS
    // the deck builds
 const ROM_CONTENT = document.getElementById("ROM_SCREEN");

    ROM_CONTENT.innerHTML = `
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


    CurrentDeck = null;
        CurrentDeck = DECK[Math.floor(Math.random() * DECK.length)];

    console.log(CurrentDeck.deckname + ": " + CurrentDeck.cards.length + " Cards");
    
    ROM_Header.innerHTML = "<h1 id='title'>" + MRA001.toy + "</h1>";

    AppControls.innerHTML = "<button class='gameBtn' onclick'RandomDeck()'>Random Deck</button>";
    AppControls.innerHTML += "<button class='gameBtn' onclick='Draw1()'>Draw A Card</button>";
    AppControls.innerHTML += "<button class='gameBtn'>Rules & Credits</button>";


const handle = document.getElementById("grabby")
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


// quick set for testing
//LoadROM(GameTitle);
/*
"MESSAGE BOARD",
        "LOST PASSWORD",
        "EMAIL",
        "DEAD LINK",
        "POP UP AD",
        "MIRRORED SITE",
        "CHAIN EMAIL",
        "RABBIT HOLE",
        "MISSING IMAGE BOX",
        "AVATAR",
        "USERNAME",
        "WEBRING",
        "USER UNKNOWN",
        "CHAT ROOM",
        "PERSONAL HOMEPAGE"
    */