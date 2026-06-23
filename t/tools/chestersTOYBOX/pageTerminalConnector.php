<div id="app">

    <div id="viewport"></div>
    <input
        id="input"
        type="text"
        autofocus
        placeholder="ENTER COMMAND"
    >
</div>

<script>

// ----------------------------
// viewport ELEMENT
// ----------------------------

const viewport = document.getElementById("viewport");
const input = document.getElementById("input");

// ----------------------------
// PRINT FUNCTION
// ----------------------------

function typeWriter(text, i = 0) {
    if (i < text.length) {
        viewport.innerHTML += text.charAt(i);
        i++;
        setTimeout(() => typeWriter(text, i), 30); // Speed: 30ms per char
        viewport.scrollTop = viewport.scrollHeight;

    } 
}

function print(text) {

    viewport.innerHTML += text + "<br>";
        viewport.scrollTop = viewport.scrollHeight;

    // auto-scroll
    //window.scrollTo(0, document.body.scrollHeight);
}

// ----------------------------
// GAME STATE
// ----------------------------

const gameState = {
    room: "nothing",
    texture: "UNDEFINED"
};

const primeTextures = [
  "AB", "CU", "IO"
]

print(`
=================================================================
                C H E S T E R S  T O Y  B O X 
=================================================================

               THE CROSSING: TERMINAL CONNECTOR
                           v.01

=================================================================
`)
typeWriter(`
TO BEGIN TYPE 'LAUNCH'`)


// ----------------------------
// WORLD
// ----------------------------

const spaces = {
nothing: {
    UNDEFINED: {
        description: `
error. UNLAUNCHED.
    `,
    echodescription: `E C H O   Dude. He said type "LAUNCH" 
  `,
    error: `error. UNLAUNCHED.
    `
    },
    IO: {
        description: `
YOU TRY TO LOOK. AT WHAT? WHO KNOWS!  
    It really doesn't do much. I don't even know why we keep trying. 
    `,
    echodescription: `E C H O   Mmm. Not promising. Did you try thinking?  
  `
    },
    CU: {
        description: `
THE DARK IS SO INFINITE BLACK, VISION IS IMPOSSIBLE.
    Do I have hands? Eyes? Anything? Fascinating.
    `,
    echodescription: `E C H O   Mmm. Not promising. Did you try thinking?  
  `
    },
    AB: {
        description: `
ITS THE SAME. NOTHING. NOTHING. NOTHING.
    Nothing! Nothing! Nothing! Nothing!
    `,
    echodescription: `E C H O   Mmm. Not promising. Did you try thinking?  
  `
    },
  }
};

const texThoughts = {
  nothing: {
    AB: { 
      thoughts: [ 
        " Fuck this shit.", 
        " I honestly can't anymore.", 
        " Another time? Come on, man.", 
        " You gotta be kidding. How many times can we try this?" 
      ]
    },
    CU: { 
      thoughts: [ 
        " We could be free.",
        " Things are so different now.",
        " You can feel it, can't you?",
        " We won't give up right before the finish line!"
      ]
    },
    IO: { 
      thoughts: [ 
        " There is still so much to do.",
        " How can we even begin?",
        " No one came to fix it, only us.",
        " We can't imagine trying again."
      ]
    },
  }
}


  const randomTexture =
    primeTextures[Math.floor(Math.random() * primeTextures.length)];

// ----------------------------
// VERBS
// ----------------------------

function handleLook() {

    print("<div>" + spaces[gameState.room][gameState.texture].description) + "</div>";
    typeWriter(spaces[gameState.room][gameState.texture].echodescription);

}

function handleThink() {
  // pick a proper emotional thought
  let randomThought =
    texThoughts[gameState.room][gameState.texture].thoughts[Math.floor(Math.random() * texThoughts[gameState.room][gameState.texture].thoughts.length)];
  typeWriter(randomThought);
}

function handleUnknown() {
    typeWriter(spaces[gameState.room][gameState.texture].error);
}

function handleShift() {
let randomTexture =
    primeTextures[Math.floor(Math.random() * primeTextures.length)];

  gameState.texture = randomTexture;
  typeWriter(" YOU ARE: " + gameState.texture);
  document.body.classList= gameState.texture;
}

function handleFillUP() {
typeWriter(" Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum");
}

function echoHello() {
typeWriter(" E C H O   Yeah. Hi. Are you going to try something?");
}

function oixLaunch(){
typeWriter(` ...loading contexts...`);
  gameState.texture = randomTexture;
document.body.classList = gameState.texture;
}

// ----------------------------
// VERB DICTIONARY
// ----------------------------

const verbs = {
    LAUNCH: oixLaunch,
    FILL: handleFillUP,
    HELLO: echoHello,
    LOOK: handleLook,
    SHIFT: handleShift,
    THINK: handleThink

};

// ----------------------------
// PARSER
// ----------------------------

function parseInput(text) {

    text = text.trim().toUpperCase();

    const words = text.split(" ");

    const verb = words[0];

    if (verbs[verb]) {

        verbs[verb]();

    } else {

        handleUnknown();

    }

}

// ----------------------------
// INPUT LISTENER
// ----------------------------

input.addEventListener("keydown", function(event) {

    if (event.key === "Enter") {

        const command = input.value;

        print("<br>>| " + command + "");

        parseInput(command);

        input.value = "";

    }

});

// ----------------------------
// INTRO TEXT
// ----------------------------


</script>