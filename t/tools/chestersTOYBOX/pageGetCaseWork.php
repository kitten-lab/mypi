

<style>
.game-screen {
  position: relative;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  width: 1024px;
  border: 1px solid white;
  padding: 20px;
}

customer p {
    border-bottom: 1px;
}

.atmosphere {
    position:relative;
    top: 20px;
    right: 20px;
}
.special {
    font-weight: 600;
    color: red;
}

.high {
    background-color: rgba(251, 255, 0, 0.22);
}

story {
    position: relative;
}

span {
    display: table-row;
}

.tile {
    color: transparent;
    display: flex;
    justify-content: center; /* Horizontal centering */
    align-items: center;
    height: 100px;
    width: 100px;
    flex: 0 0 19%; 
    box-sizing: border-box;
    text-align: center;
    margin:2px;
}
.reels {
    display: flex;
    flex-wrap: wrap;
    width: 500px;
}

.SP {
  background-color: rgb(83, 0, 0);
  border: 2px solid rgb(97, 43, 43);
  box-shadow: inset 0px 0px 1px rgb(84, 14, 14);
  background: repeating-linear-gradient(90deg, #570707, #a70808 80px);
  
}

.SP::after {
    position: relative;
    content: "CASE #3";
    color: rgb(178, 109, 109) !important;
    margin-left: -20px;
}

.HP1 {

  background-color: blue;
  border: 4px solid #0e0c5e;
  background: repeating-linear-gradient(45deg, #312b60, #273e6f 10px, #312b60 10px, #0e0c5e 20px);
}
.HP2 {

  background-color: rgb(81, 105, 81);
  border: 4px solid #3d443b47;
  background-image: radial-gradient(#3d443b47 8px, transparent 2px);
  background-size: 20px 20px;
}
.MP1 {

  background-color: #b3339acd;
  border: 3px solid #ffffff99;
}
.MP2 {

  background-color: #2a87159e;
  border: 3px solid #ffffff99;
}
.MP3 {

  background-color: #b07556a0;
  border: 3px solid #ffffff99;
}
.LP1 {

  background-color: #ffaeee33;
  border: 2px solid #ffffff99;
}
.LP2 {

  background-color: #bdffae33;
  border: 2px solid #ffffff99;
}
.LP3 {

  background-color: #3d3647;
  border: 2px solid #ffffff99;
}
.LP4 {

  background-color: #3f394f;
  border: 2px solid #ffffff99;
  background-image: 
    linear-gradient(rgb(88, 11, 11) 1px, transparent 1px),
    linear-gradient(90deg, rgb(94, 16, 16) 1px, transparent 1px);
  background-size: 20px 20px;
}


.match {
    border: 5px solid rgb(255, 255, 255);
    background-color: rgba(255, 255, 255, 0.429);
    box-shadow: 0 0 5px white;
}
</style>

    </head>
<div id="wrapper" class="office">

<div class="game-screen">
<h2 id="case" class="heading"></h2>
<span id="atmosphere" class='atmosphere'>ATMOSPHERE: The office at night.</span>
<div id="reels" class="reels"></div>

<div class="story">
<p id="major_clue"></p>
<p id="customer"></p>
<p id="clue"></p>
</div>

<p id="last_spin"></p>

</div>
<button onclick="spin()">SPIN</button>
<script>
const symbols = ["SP", "HP1", "HP2", "MP1", "MP2", "MP3", "LP1", "LP2", "LP3", "LP4"]
const atmosphere = [
    "Rain pounds outside.",
    "There is the subtle sense of a heartbeat.",
    "My head pounds. I try to focus."
]

const cases = [
  {
    title: "The Unattended Reservation",
    slug: "red_lady1",
    customer: "A Woman in a Red Dress",
    clues: [
      "OBSERVATION: She is flustered and slightly damp. It must be raining pretty well tonight.",
      "'I was told they would be there. No one arrived.'",
      "The table was reserved. No name attached.",
      "She kept checking the door.",
      "Two glasses. One untouched.",
      "Someone paid before she got there.",
      "Case Closed"
    ]
  },
  {
    title: "The Man Who Never Left",
    slug: "invis_man1",
    customer: "The Hotel Manager",
    clues: [
      "He checked in three nights ago.",
      "Room's empty. Always is.",
      "Key keeps turning up at the desk.",
      "No one saw him leave.",
      "The bed is still warm.",
      "Case Closed"
    ]
  }
]

document.getElementById("case").innerText = "The Office"

let currentCase = null
let clueIndex = 0
spin()

function spin() {
  // render 5x3 grid
  let output = ""
  let all_spins = []
  for (let i = 0; i < 15; i++) {
    let rand = Math.floor(Math.random() * symbols.length)
    last_spin = symbols[rand]
    all_spins.push(last_spin)
    output += "<div id='" + last_spin + "' class='tile " + last_spin + "'>" + last_spin + "</div>"
    if ((i + 1) % 5 === 0) output += "<br>"
  }
  document.getElementById("reels").innerHTML = output
  console.log(all_spins);
  
  let counts = {}

  for (let symbol of all_spins) {
    if (!counts[symbol]) {
        counts[symbol] = 1 

    } else {
        counts[symbol]++
    }
  }

  console.log(counts)
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
  
  console.log(result)

  if (result.length > 0) {
   if (!currentCase) {
      currentCase = cases[Math.floor(Math.random() * cases.length)]
      clue_count = 0
      clueIndex = 0
      document.getElementById("case").innerText = "CASE: " + currentCase.title
      document.getElementById("customer").innerText = "CUSTOMER: " + currentCase.customer
      document.getElementById("wrapper").classList = currentCase.slug
    }

    if (clueIndex < currentCase.clues.length) {


        const lowFound = result.find(item => item.includes("LP"));
        if (lowFound) {
        let rand = Math.floor(Math.random() * atmosphere.length)
        document.getElementById("atmosphere").innerText = "ATMOSPHERE: " + atmosphere[rand]
        }

        const mediumFound = result.find(item => item.includes("MP"));
        if (mediumFound) {
        document.getElementById("clue").innerHTML += "<span id='" + clue_count + "'>" + currentCase.clues[clueIndex] + "</span"
        clueIndex++

        }

        const found = result.find(item => item.includes("SP"));
        if (found) {
        document.getElementById("clue").innerHTML += "<span id='" + clue_count + "'>" + currentCase.clues[clueIndex] + "</span"
        clueIndex++
        document.getElementById(clue_count).classList.add('special')
        }

        const hifound = result.find(item => item.includes("HP"));
        if (hifound) {
        document.getElementById("clue").innerHTML += "<span id='" + clue_count + "'>" + currentCase.clues[clueIndex] + "</span"
        clueIndex++
        document.getElementById(clue_count).classList.add('high')
        
        }

    clue_count++
    
    } else {
      document.getElementById("clue").innerText = ""
      document.getElementById("case").innerText = "The Office"
      document.getElementById("customer").innerText = ""
      document.getElementById("atmosphere").innerText = "ATMOSPHERE: The office at night."
      currentCase = null
      document.body.classList = "office"
    }
    }
}
</script>
