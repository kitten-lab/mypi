"""
mypi pocket browser — one host (b), many SYS paths.

  python pocket_browser.py

Everything is http://b/{sys}/… under the hood.
The window chrome never shows that host leadline — title is path-only
(e.g. starline/news/headlines). No new fake domains when you add surfaces.
"""

from __future__ import annotations

import sys
from pathlib import Path
from urllib.parse import urlparse

try:
    import webview
except ImportError:
    print("pip install pywebview")
    sys.exit(1)

HERE = Path(__file__).resolve().parent
LAUNCHER = (HERE / "launcher.html").as_uri()
HOME = LAUNCHER
B = "http://b"
TITLE_ROOT = "mypi"


def path_title(url: str) -> str:
    """Human leadline: path only, no scheme/host. file:// launcher → gate."""
    if not url:
        return TITLE_ROOT
    if url.startswith("file:") or "launcher.html" in url:
        return f"{TITLE_ROOT} · gate"
    try:
        p = urlparse(url)
        path = (p.path or "/").rstrip("/") or "/"
        # drop empty first segment from leading /
        display = path.lstrip("/") or "/"
        if p.query:
            display = f"{display}?{p.query}"
        return f"{TITLE_ROOT} · {display}"
    except Exception:
        return TITLE_ROOT


def main() -> None:
    window = webview.create_window(
        title=f"{TITLE_ROOT} · gate",
        url=HOME,
        width=1100,
        height=720,
        min_size=(640, 480),
        background_color="#0a100e",
        text_select=True,
    )

    def go(path: str) -> None:
        window.load_url(B.rstrip("/") + "/" + path.lstrip("/"))

    def on_loaded() -> None:
        try:
            url = window.get_current_url() or ""
            window.set_title(path_title(url))
        except Exception:
            pass

    menu_items = [
        webview.menu.MenuAction("Home (gate)", lambda: window.load_url(HOME)),
        webview.menu.MenuAction("WWW · danyi", lambda: go("www/danyi/index")),
        webview.menu.MenuAction("Starline News", lambda: go("starline/news/headlines")),
        webview.menu.MenuAction("Crates", lambda: go("starline/chester/crates")),
        webview.menu.MenuAction("Charlie", lambda: go("starline/charlie/threads")),
        webview.menu.MenuAction("TPS", lambda: go("starline/satora/shelves")),
        webview.menu.MenuAction("Book Oriel", lambda: go("book/terminal_girls/oriel")),
        webview.menu.MenuAction("Port b", lambda: window.load_url(B + "/")),
        webview.menu.MenuAction("Reload", lambda: window.evaluate_js("location.reload()")),
        webview.menu.MenuAction("Back", lambda: window.evaluate_js("history.back()")),
        webview.menu.MenuAction("Forward", lambda: window.evaluate_js("history.forward()")),
    ]

    window.events.loaded += on_loaded

    try:
        menu = webview.menu.Menu("mypi", menu_items)
        webview.start(menu=[menu], debug=False)
    except Exception:
        webview.start(debug=False)


if __name__ == "__main__":
    main()
