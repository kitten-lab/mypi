/**
 * webBAR kitten — address leadline for WWW explorer chrome.
 * Pocket browser (pywebview) is picky: no host k fetch required if inlined;
 * use textContent (not innerHTML); navigate with assign().
 */
(function () {
  function barEl() {
    return document.getElementById("wwwBar");
  }

  function pathOnly() {
    return (window.location.pathname || "/") + (window.location.search || "");
  }

  function paintBar() {
    var el = barEl();
    if (!el) return;
    // contenteditable: textContent, not innerHTML (WebView inserts <br> etc.)
    el.textContent = pathOnly();
  }

  window.WWWBack = function WWWBack() {
    if (window.history.length > 1) {
      window.history.go(-1);
    } else {
      // pocket menu load_url often has no history — soft fallback
      window.history.back();
    }
  };

  window.WWWForward = function WWWForward() {
    window.history.go(1);
  };

  window.LetsGO = function LetsGO() {
    var el = barEl();
    if (!el) return;
    var raw = (el.textContent || el.innerText || "").trim();
    // contenteditable can inject newlines / zero-width junk
    raw = raw.replace(/\u200b/g, "").replace(/\s+/g, " ").trim();
    if (!raw) return;

    if (/^https?:\/\//i.test(raw)) {
      window.location.assign(raw);
      return;
    }

    // allow "b://www/danyi/index" play-URL style from old chrome demos
    if (/^b:\/\//i.test(raw)) {
      raw = raw.replace(/^b:\/\//i, "/");
    }

    if (raw.charAt(0) !== "/") {
      raw = "/" + raw;
    }

    // same-origin path nav (works on http://b and pocket webview)
    window.location.assign(raw);
  };

  var el = barEl();
  if (!el) {
    console.warn("webBAR: #wwwBar missing");
    return;
  }

  paintBar();

  el.addEventListener("keydown", function (event) {
    if (event.key === "Enter") {
      event.preventDefault();
      window.LetsGO();
    }
  });

  el.addEventListener("input", function () {
    try {
      localStorage.setItem("savedContent", el.textContent || "");
    } catch (e) { /* private mode / file origin */ }
  });

  // wire toolbar divs if they lack reliable onclick in webview
  document.querySelectorAll(".wwwExplorer_linkBar [onclick]").forEach(function (node) {
    /* leave existing onclick */
  });
  var go = document.getElementById("GO");
  if (go && !go.__webbarBound) {
    go.__webbarBound = true;
    go.addEventListener("click", function (e) {
      e.preventDefault();
      window.LetsGO();
    });
  }

  console.log("%cMEOW MEOW! WWWBar IS ACTIVATE", "color:pink");
})();
