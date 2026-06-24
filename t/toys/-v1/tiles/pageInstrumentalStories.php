<section id="app">

<div id="reels" class="reels"></div>

  
<div id="game-screen">

<div class="flex-container">
  <div class="column">
<h1 id="case" class="heading"></h1>
</div>
  <div class="column" style="text-align: right;">
  <button class="spin" onclick="spin()">PLAY</button>
<button class="audio" onclick="unmuteAll()">ACTIVATE SOUND</button>
</div>
</div>


<div class="major_clue">
<h3 id="customer" class="heading"></h3>
</div>

<div class="clue" id="clue">
<div id="bio"></div>
</div>

<p id="last_spin"></p>
</div>


</div>

</div>



</section>

<script src="https://cdn.jsdelivr.net/npm/tone@15.1.22/build/Tone.min.js"></script>

<script>

 
let synth;

async function playSound() {
    await Tone.start();
    if (!synth) {
        synth = new Tone.PolySynth(Tone.Synth, {
            maxPolyphony: 8,
            oscillator: { frequency: 440, type: "sawtooth6" },
            envelope: {
                attack: 3,
                decay: 0.5,
                sustain: 0.05,
                release: 1
            }
        }).toDestination();
    }
}

const symbols = ["SP", "HP1", "HP2", "MP1", "MP2", "MP3", "LP1",  "LP2", "LP3", "LP4", ]
const atmosphere = [
    "Rain pounds outside.",
    "There is the subtle sense of a heartbeat.",
    "My head pounds. I try to focus."
]

const scale = [
"C4","D5","E4","F5","G4","A5","B4", 
"C5","D4","E5","F4","G5","A4","B5", "C6"
]

const cases = [
  {
    title: "The Smudged Man",
    slug: "casey1",
    customer: "Some Mary",
    clientBio: "A woman of an average age. Just some mary in a red dress wearing a necklace with a lamb pendant.",
    clues: [
      "It's a cold Tuesday. Every Tuesday's been cold for decades now.",
      "A woman walks into the Office at 78 Case Street. Sour face. Sour hands.",
      "I ask her how I can help. She looks bothered I even exist.",
      "I wonder what drives a woman to ask for help in a place that clearly disgusts her.",
      "She hands me a set of keys and a black wallet.",
      "She leaves without a word.",
      "I check the wallet.",
      "It contains an ID but the face is blurred. So is most of the name.",
      "I can make out only a C starting the first name, and nothing more.",
      "There is a business card in the wallet.",
      "The card reads 'ChesterImports.com! Now importing ~anything~.'",
      "There is no indication that any of this means anything. (end)"
    ]
  },
  {
    title: "The Unattended Reservation",
    slug: "red_lady1",
    clientBio: "A wild-eyed woman who appears to not slept in weeks. Her hair is in a tight bun.",
    customer: "The Red Lady",
    clues: [
      "She is flustered and slightly damp. It must be raining pretty well tonight.",
      "There was anger, almost fear in her eyes.",
      "'I was told they would be there. No one arrived.'",
      "I ask her what's wrong. She said she'd been invited to a special meeting.",
      "'Something went wrong. Something changed.' she said.",
      "'No one came, but everything changed.'",
      "I asked her to elaborate on the change.",
      "She wouldn't exactly say. She only redirected to the meeting.",
      "'The table was reserved. It should have happened correctly.' she said.",
      "The only thing she was certain of was that there was 'Two glasses. One untouched.'",
      "And yet no one ever arrived.",
      "I called the resturant. The host looked briefly at the previous bookings..",
      "'Yup, see the reservation right here. For 3. No name attached.'",
      "I called the restaurant one more time. Surely a name on the bill existed.",
      "No name. Paid in cash. Someone had paid before she got there.",
      "I called her back into the office a few nights later.",
      "She was drier now, both physically, and in tone.",
      "'It's only gotten worse.' she said.",
      "She kept checking the door.",
      "'Got somewhere else to be?' I asked. She didn't reply.",
      "With no other leads to go on, and an uncoperative client, I close the case.",
      "If it could even be called that. (end)"
    ]
  },
  {
    title: "The Man Who Never Left",
    slug: "invis_man1",
    customer: "The Hotel Manager",
    clientBio: "A man with a stone face.",
    clues: [
      "He checked in three nights ago.",
      "Room's empty. Always is.",
      "Key keeps turning up at the desk.",
      "No one saw him leave.",
      "The bed is still warm.",
    ]
  }
]



document.getElementById("case").innerText = "The Office"

let currentCase = null
let clueIndex = 0

function deadSpin() {
 // render 5x3 grid
  let output = ""
  for (let i = 0; i < 15; i++) {
    let rand = Math.floor(Math.random() * symbols.length)
    output += "<div id='" + last_spin + "' class='tile " + symbols[rand] + "' data-index='" + i + "'>" + symbols[rand] + "</div>"
    //if ((i + 1) % 5 === 0) output += "<br>"
  }
  document.getElementById("reels").innerHTML = output
}

deadSpin();


function spin() {

playSound();

  // render 5x3 grid
  let output = ""
  let all_spins = []
  for (let i = 0; i < 15; i++) {
    let rand = Math.floor(Math.random() * symbols.length)
    last_spin = symbols[rand]
    all_spins.push(last_spin)
    output += "<div id='" + last_spin + "' class='tile " + last_spin + "' data-index='" + i + "'>" + last_spin + "</div>"
    //if ((i + 1) % 5 === 0) output += "<br>"
  }
  document.getElementById("reels").innerHTML = output
  
  let counts = {}

  for (let symbol of all_spins) {
    if (!counts[symbol]) {
        counts[symbol] = 1 

    } else {
        counts[symbol]++
    }
  }


  let result = []

  for (let symbol in counts) {
    if (counts[symbol] >= 4) {
        last_result = counts[symbol] + " " + symbol
        result.push(last_result)
        let elements = document.querySelectorAll('.' + symbol)
          elements.forEach(el => {
            el.classList.add('match')
            })
        }
    }
  
  

  if (result.length > 0) {


  const matched = document.querySelectorAll(".match");

  let notes = [];

  matched.forEach(el => {
    const index = parseInt(el.dataset.index);
    notes.push(scale[index]);
    });
    
  if (notes.length > 0) {
      synth.triggerAttackRelease(notes, "3n");
  }


   if (!currentCase) {
      currentCase = cases[Math.floor(Math.random() * cases.length)]
      clue_count = 1
      clueIndex = 0
      document.getElementById("customer").innerText = "CASE: " + currentCase.title
      document.getElementById("case").innerText = "CLIENT: " + currentCase.customer
      document.getElementById("app").classList = currentCase.slug
      document.getElementById("bio").innerHTML = "<span class='frag'>" + currentCase.clientBio + "</span>"
    }

    if (clueIndex < currentCase.clues.length) {


        const lowFound = result.find(item => item.includes("LP"));
        if (lowFound) {
        let rand = Math.floor(Math.random() * atmosphere.length)
        document.getElementById("clue").innerHTML +=  "<span class='frag' id='" + clue_count + "'><span class='fragNm'>" + clue_count + "</span>" + atmosphere[rand]
        document.getElementById(clue_count).classList.add('feel')
        }

        const mediumFound = result.find(item => item.includes("MP"));
        if (mediumFound) {
        document.getElementById("clue").innerHTML += "<span class='frag' id='" + clue_count + "'><span class='fragNm'>" + clue_count + "</span>" + currentCase.clues[clueIndex] + "</span>"
        document.getElementById(clue_count).classList.add('clue')
        clueIndex++

        }

        const found = result.find(item => item.includes("SP"));
        if (found) {
        document.getElementById("clue").innerHTML +=  "<span class='frag' id='" + clue_count + "'><span class='fragNm'>" + clue_count + "</span>" + currentCase.clues[clueIndex] + "</span>"
        document.getElementById(clue_count).classList.add('special')
        clueIndex++
        }

        const hifound = result.find(item => item.includes("HP"));
        if (hifound) {
        document.getElementById("clue").innerHTML += "<span class='frag' id='" + clue_count + "'><span class='fragNm'>" + clue_count + "</span>" + currentCase.clues[clueIndex] + "</span>"
        document.getElementById(clue_count).classList.add('high')
        clueIndex++
        }

    clue_count++
    
    } else {
      document.getElementById("case").innerHTML = ""
      
      currentCase = null
      currentCase = cases[Math.floor(Math.random() * cases.length)];

      clue_count = 1
      clueIndex = 0

      document.getElementById("customer").innerText = "CASE: " + currentCase.title
      document.getElementById("case").innerText = "CLIENT: " + currentCase.customer
      document.getElementById("app").classList = currentCase.slug
      document.getElementById("bio").innerHTML = "<span class='frag'>" + currentCase.clientBio + "</span>"
    }
    }
}


</script>
