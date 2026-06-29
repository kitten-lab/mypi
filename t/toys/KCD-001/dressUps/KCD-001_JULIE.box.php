<JULIE id='VEN_JULIE' class='KCD-001' onclick='ToggleKCD001()'>
  <JULIE_CASE id='KCD-001' class="Julie">
    <label id="KCD-001-label" class='label'>
      <display id="KCD-001-title" class='title'>Detective Kat Moire</display>
      <funtitle id="KCD-001-funtitle" class='funtitle'>Keys and Chords</funtitle>
    </label>
  </JULIE_CASE>
</JULIE>


<script>
  

const handle = document.getElementById("VEN_JULIE")
const draggable = document.getElementById("VEN_JULIE");

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

</script>