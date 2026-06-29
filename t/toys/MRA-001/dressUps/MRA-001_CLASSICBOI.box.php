<CLASSICBOI id='VEN_CLASSICBOI' class='ROMCover' onclick='ToggleMRA001()'>
  <CLASSICBOI_CASE id='MRA-001' class="CLASSICBOI">
    <label id="MRA-001-label" class='label'>
      <display id="MRA-001-title" class='title'>Morana Arcana</display>
      <funtitle id="MRA-001-funtitle" class='funtitle'>Fortuna Snackums</funtitle>
    </label>
  </CLASSICBOI_CASE>
</CLASSICBOI>


<script>
  

const handle = document.getElementById("VEN_CLASSICBOI")
const draggable = document.getElementById("VEN_CLASSICBOI");

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