document.getElementById("wwwBar").innerHTML = window.location.pathname
function WWWBack(){ javascript:history.go(-1) }
function WWWForward(){ javascript:history.go(1) }

const wwwBAR = document.getElementById("wwwBar")

function LetsGO(){
    window.location.href = wwwBAR.innerHTML;
}

wwwBAR.addEventListener('input', () => {
    localStorage.setItem('savedContent', wwwBAR.innerHTML);
});

wwwBAR.addEventListener("keydown", function(event) {
  if (event.key === "Enter") {
    event.preventDefault(); 
    LetsGO();
  }
});

   console.log("%cMEOW MEOW! WWWBar IS ACTIVATE", "color:pink");