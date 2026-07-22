/**
 * Add room flavor as a class — never REPLACE body classList.
 * Old `classList = TEXTURE` wiped shell classes (book-atelier, pocket-app, …)
 * or threw; either way it fights app chrome.
 */
(function () {
  var flavor = typeof TEXTURE !== "undefined" ? String(TEXTURE || "").trim() : "";
  if (flavor && document.body) {
    document.body.classList.add("room-texture", "texture-" + flavor.replace(/[^\w-]+/g, "-"));
    if (/^[\w-]+$/.test(flavor)) {
      document.body.classList.add(flavor);
    }
  }
  console.log("%cMEOW MEOW! roomTEXTURE kitten is ONLINE!", "color:pink");
  console.log("    MEOW! THE roomTEXTURE IS: ", flavor);
})();