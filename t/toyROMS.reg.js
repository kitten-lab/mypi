ROM_SCREEN = document.getElementById("ROM_SCREEN");

window.ROM_SHELLS = {
    Julie: {
    name: "Julie",
    divLayout: `<JULIE id='VEN_JULIE' class='ROMCover' onclick='ToggleVEN()'>
    <JULIE_CASE id='` + ActiveVEN.vencode + `'>
    <label class='label'>
      <display class='title'>` + ActiveVEN.display + `</display>
      <funtitle class='funtitle'>` + ActiveVEN.funtitle + `</funtitle>
    </label>
    </JULIE_CASE>`
    },
    ClassicBoi: {
    name: "Classic Boi",
    divLayout: `<CLASSICBOI id='VEN_CLASSICBOI' class='ROMCover' onclick='ToggleVEN()'>
    <CLASSICBOI_CASE id='` + ActiveVEN.vencode + `'>
    <label class='label'>
      <display class='title'>` + ActiveVEN.display + `</display>
      <funtitle class='funtitle'>` + ActiveVEN.funtitle + `</funtitle>
    </label>
    </CLASSICBOI_CASE>`
    }
}