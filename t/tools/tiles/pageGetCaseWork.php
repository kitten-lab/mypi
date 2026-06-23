
<body class="office">
<div class="clue" id="viewport"></div>

<div id="reels" class="reels"></div>
<button class="spin" onclick="spin()">SEARCH</button>

<div class="flex-container">
  <div class="column2">

<h2 id="case" class="heading"></h2>
<span id="atmosphere" class='atmosphere'>ATMOSPHERE: The office at night.</span>
<p id="customer"></p>

  </div>
  <div class="column">

<div class="game-nall_spinsative">
<p id="major_clue"></p>


<p id="last_spin"></p>
</div>


  </div>
</div>

<div id="app">




</div>


<script>
const symbols = ["SP", "HP1", "HP2", "MP1", "MP2", "MP3", "SP",  "MP1", "MP2", "MP3", "HP1", "HP2", "LP1","SP",  "LP2", "LP3", "LP4", "LP1", "LP2", "LP3", "LP4", ]
const atmosphere = [
    "Rain pounds outside.",
    "There is the subtle sense of a heartbeat.",
    "My head pounds. I try to focus."
]

const cases = [
  {
    title: "The Smudged Man",
    slug: "casey1",
    customer: "Some Mary",
    clues: [
      "It's a cold Tuesday. Every Tuesday's been cold for decades now. A woman walks into the Office at 78 Case Street. Sour face. Sour hands.",
      "I ask her how I can help. She looks bothered I even exist. I wonder what drives a woman to ask for help in a place that clearly disgusts her.",
      "She hands me a set of keys and a black wallet.",
      "She leaves without a word.",
      "I check the wallet. It contains an ID but the face is blurred. So is most of the name. I can make out only a C starting the first name, and nothing more.",
      "There is a business card in the wallet.",
      "ChesterImports.com! Now importing ~anything~.",
      "There is no indication that any of this means anything. Case Closed."
    ]
  },
  {
    title: "The Unattended Reservation",
    slug: "red_lady1",
    customer: "The Red Lady",
    clues: [
      "OBSERVATION: She is flustered and slightly damp. It must be raining pretty well tonight.",
      "There was anger, almost fear in her eyes. 'I was told they would be there. No one all_spinsived.'",
      "I ask her what's wrong. She said she'd been invited to a special meeting.",
      "'Something went wrong. Something changed.' she said. 'No one came, but everything changed.'",
      "I asked her to elaborate on the change. She wouldn't exactly say. She only redirected to the meeting.",
      "'The table was reserved. It should have happened correctly.' she said.",
      "The only thing she was certain of was that there was 'Two glasses. One untouched.' And yet no one ever all_spinsived.",
      "I called the resturant. The host looked briefly at the previous bookings..",
      "'Yup, see the reservation right here. For 3. No name attached.'",
      "I called the restaurant one more time. Surely a name on the bill existed.",
      "No name. Paid in cash. Someone had paid before she got there.",
      "I called her back into the office a few nights later. She was drier now, both physically, and in tone.",
      "'It's only gotten worse.' she said.",
      "She kept checking the door. 'Got somewhere else to be?' I asked. She didn't reply.",
      "With no other leads to go on, and an uncoperative client, I close the case, if it could even be called that."
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
    //if ((i + 1) % 5 === 0) output += "<br>"
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
      clue_count = 1
      clueIndex = 0
      document.getElementById("case").innerText = "CASE: " + currentCase.title
      document.getElementById("customer").innerText = "CUSTOMER: " + currentCase.customer
      document.body.classList = currentCase.slug
    }

    if (clueIndex < currentCase.clues.length) {


        const lowFound = result.find(item => item.includes("LP"));
        if (lowFound) {
        let rand = Math.floor(Math.random() * atmosphere.length)
        document.getElementById("atmosphere").innerText = "ATMOSPHERE: " + atmosphere[rand]
        document.getElementById("viewport").innerHTML += "<span class='frag' id='" + clue_count + "'> Fragment " + clue_count + ": ATMOSPHERE: " + atmosphere[rand]
        }

        const mediumFound = result.find(item => item.includes("MP"));
        if (mediumFound) {
        document.getElementById("viewport").innerHTML += "<span class='frag' id='" + clue_count + "'> Fragment " + clue_count + ": " + currentCase.clues[clueIndex] + "</span>"
        clueIndex++

        }

        const found = result.find(item => item.includes("SP"));
        if (found) {
        document.getElementById("viewport").innerHTML += "<span class='frag' id='" + clue_count + "'> Fragment " + clue_count + ": KEY: " + currentCase.clues[clueIndex] + "</span>"
        document.getElementById(clue_count).classList.add('special')
        clueIndex++
        }

        const hifound = result.find(item => item.includes("HP"));
        if (hifound) {
        document.getElementById("viewport").innerHTML += "<span class='frag' id='" + clue_count + "'> Fragment " + clue_count + ": CLUE: " + currentCase.clues[clueIndex] + "</span>"
        document.getElementById(clue_count).classList.add('high')
        clueIndex++
        
        }

    clue_count++
    
    } else {
      document.getElementById("viewport").innerText = ""
      document.getElementById("case").innerText = "The Office"
      document.getElementById("customer").innerText = ""
      document.getElementById("atmosphere").innerText = "ATMOSPHERE: The office at night."
      currentCase = null
      document.body.classList = "office"
    }
    }
}
</script>

</body>
</html>