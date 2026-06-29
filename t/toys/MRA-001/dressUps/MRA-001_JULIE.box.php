<JULIE id='VEN_JULIE' class='ROMCover' onclick='ToggleMRA001()'>
  <JULIE_CASE id='MRA-001' class="Julie">
    <label id="MRA-001-label" class='label'>
      <display id="MRA-001-title" class='title'>Morana Arcana</display>
      <funtitle id="MRA-001-funtitle" class='funtitle'>Fortuna Snackums</funtitle>
    </label>
  </JULIE_CASE>
</JULIE>

<script>
  

const handle2 = document.getElementById("VEN_JULIE")
const draggable2 = document.getElementById("VEN_JULIE");

let offsetX, offsetY;

handle2.addEventListener('mousedown', (e) => {
    // Calculate the offset position
    offsetX = e.clientX - draggable2.offsetLeft;
    offsetY = e.clientY - draggable2.offsetTop;

    // Add event listeners to the document for mousemove and mouseup
    document.addEventListener('mousemove', mouseMoveHandler);
    document.addEventListener('mouseup', mouseUpHandler);
});

function mouseMoveHandler(e) {
    // Update the position of the draggable element
    draggable2.style.left = `${e.clientX - offsetX}px`;
    draggable2.style.top = `${e.clientY - offsetY}px`;
    draggable2.style.position = 'absolute'; // Set position to absolute
}

function mouseUpHandler() {
    // Remove event listeners when mouse is released
    document.removeEventListener('mousemove', mouseMoveHandler);
    document.removeEventListener('mouseup', mouseUpHandler);
}

</script>