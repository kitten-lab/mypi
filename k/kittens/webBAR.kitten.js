/**
 * webBAR kitten — address leadline for explorer chrome (WWW, STARLINE, …).
 * Pocket browser (pywebview) is picky: no host k fetch required if inlined;
 * use textContent (not innerHTML); navigate with assign().
 *
 * Abilities: back, forward, soft refresh, hard refresh (cache-bust), GO.
 * Hard: Ctrl+F5 / Ctrl+Shift+R, or Shift+click / Alt+click on refresh.
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

  /** Soft reload (may still hit WebView disk cache for CSS). */
  window.WWWRefresh = function WWWRefresh() {
    try {
      window.location.reload();
    } catch (e) {
      window.location.href = window.location.href;
    }
  };

  window.WWWReload = window.WWWRefresh;

  /**
   * Hard refresh — full navigation with ?_cb= so PHP getA_Style can append
   * a new ?v=filemtime._cb on host-`a` CSS (cross-origin from `b`).
   * Mutating <link> then reloading is useless: the next HTML still had bare URLs.
   */
  window.WWWHardRefresh = function WWWHardRefresh() {
    try {
      var stamp = String(Date.now());
      var dest = new URL(window.location.href);
      dest.searchParams.set("_cb", stamp);
      // avoid history spam of bust tokens
      window.location.replace(dest.toString());
    } catch (e) {
      try {
        window.location.href =
          window.location.pathname +
          "?_cb=" +
          Date.now() +
          (window.location.hash || "");
      } catch (e3) {
        window.location.reload();
      }
    }
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

  // re-sync after bfcache / soft nav if shell reuses DOM
  window.addEventListener("pageshow", function () {
    paintBar();
  });

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

  function bindRefresh(node) {
    if (!node || node.__webbarBound) return;
    node.__webbarBound = true;
    node.addEventListener("click", function (e) {
      e.preventDefault();
      if (e.shiftKey || e.altKey || e.ctrlKey || e.metaKey) {
        window.WWWHardRefresh();
      } else {
        window.WWWRefresh();
      }
    });
  }

  function bindOnce(node, fn) {
    if (!node || node.__webbarBound) return;
    node.__webbarBound = true;
    node.addEventListener("click", function (e) {
      e.preventDefault();
      fn();
    });
  }

  bindOnce(document.getElementById("GO"), window.LetsGO);
  bindRefresh(document.getElementById("REFRESH"));
  bindRefresh(document.getElementById("RELOAD"));

  // data-webbar="back|forward|refresh|hard-refresh|go"
  document.querySelectorAll("[data-webbar]").forEach(function (node) {
    if (node.__webbarDataBound) return;
    node.__webbarDataBound = true;
    var act = (node.getAttribute("data-webbar") || "").toLowerCase();
    node.addEventListener("click", function (e) {
      e.preventDefault();
      if (act === "back") window.WWWBack();
      else if (act === "forward") window.WWWForward();
      else if (act === "hard-refresh" || act === "hardrefresh") window.WWWHardRefresh();
      else if (act === "refresh" || act === "reload") {
        if (e.shiftKey || e.altKey || e.ctrlKey || e.metaKey) window.WWWHardRefresh();
        else window.WWWRefresh();
      } else if (act === "go") window.LetsGO();
    });
  });

  // keyboard: F5 soft · Ctrl+F5 / Ctrl+Shift+R hard (pocket has no browser chrome)
  if (!window.__webbarKeysBound) {
    window.__webbarKeysBound = true;
    window.addEventListener(
      "keydown",
      function (e) {
        var key = e.key || "";
        if (key === "F5" && (e.ctrlKey || e.shiftKey)) {
          e.preventDefault();
          window.WWWHardRefresh();
          return;
        }
        if (key === "F5") {
          e.preventDefault();
          window.WWWRefresh();
          return;
        }
        // Ctrl+Shift+R or Ctrl+R
        if ((key === "r" || key === "R") && (e.ctrlKey || e.metaKey)) {
          e.preventDefault();
          if (e.shiftKey) window.WWWHardRefresh();
          else window.WWWRefresh();
        }
      },
      true
    );
  }

  console.log("%cMEOW MEOW! WWWBar (+ hard refresh)", "color:pink");
})();
