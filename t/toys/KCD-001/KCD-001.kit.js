// KCD-001 — shell test only (no full game). Proves a second ROM window.

const KCD001 = {
  vencode: "KCD-001",
  provider: "Chester's Imports",
  toy: "Detective Kat Moire",
  funtitle: "Keys and Chords",
  info: "Shell test — chapter stub only.",
  funtags: ["Game", "ROM", "Detective", "ShellTest"]
};

function buildKCD001(body) {
  body.innerHTML =
    "<div class='SiloROM_BaseBox' style='width:800px;height:600px;position:relative;box-sizing:border-box;padding:12px;display:flex;flex-direction:column;'>" +
    "<h1 style='margin:0 0 8px 0;'>" +
    KCD001.toy +
    "</h1>" +
    "<p><em>" +
    KCD001.funtitle +
    "</em> — shell test. No real game yet.</p>" +
    "<div>CHAPTER 1:<br />Out of Tune, Out of Time</div>" +
    "<div class='AppControls' data-kcd-controls style='margin-top:auto;display:flex;flex-wrap:wrap;gap:6px;'></div>" +
    "</div>";

  var controls = body.querySelector("[data-kcd-controls]");
  ["Load Save File", "New Game", "Rules & Credits"].forEach(function (label) {
    var b = document.createElement("button");
    b.className = "gameBtn";
    b.textContent = label;
    controls.appendChild(b);
  });
}

function ToggleKCD001() {
  if (!window.RomHost) {
    console.warn("RomHost missing");
    return;
  }
  RomHost.toggle("KCD-001", { title: KCD001.toy + " — " + KCD001.funtitle });
}

function registerKCD001() {
  if (!window.RomHost) {
    setTimeout(registerKCD001, 30);
    return;
  }
  RomHost.register("KCD-001", buildKCD001);
}
registerKCD001();
