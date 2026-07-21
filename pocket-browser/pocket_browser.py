"""
mypi pocket browser — your internet, your window.

  cd C:\\Builds\\my-pocket-internet\\pocket-browser
  pip install pywebview
  python pocket_browser.py

Opens a desktop window onto local XAMPP Surfaces (no Chrome UI chrome).
"""

from __future__ import annotations

import sys
from pathlib import Path

try:
    import webview
except ImportError:
    print("pip install pywebview")
    sys.exit(1)

HERE = Path(__file__).resolve().parent
LAUNCHER = (HERE / "launcher.html").as_uri()
HOME = LAUNCHER


class Api:
    def home(self) -> str:
        return HOME


def main() -> None:
    api = Api()
    window = webview.create_window(
        title="mypi — pocket internet",
        url=HOME,
        width=1100,
        height=720,
        min_size=(640, 480),
        background_color="#0a100e",
        text_select=True,
        js_api=api,
    )

    def on_loaded():
        # Keep title honest when navigating into Surfaces
        pass

    window.events.loaded += on_loaded

    menu_items = [
        webview.menu.MenuAction("Home (gate)", lambda: window.load_url(HOME)),
        webview.menu.MenuAction("Starline", lambda: window.load_url("http://starline/")),
        webview.menu.MenuAction("Book", lambda: window.load_url("http://book/")),
        webview.menu.MenuAction("Port b", lambda: window.load_url("http://b/")),
        webview.menu.MenuAction("Terminal IO", lambda: window.load_url("http://go.edn/Terminal/IO/")),
        webview.menu.MenuAction("Reload", lambda: window.evaluate_js("location.reload()")),
        webview.menu.MenuAction("Back", lambda: window.evaluate_js("history.back()")),
        webview.menu.MenuAction("Forward", lambda: window.evaluate_js("history.forward()")),
    ]

    try:
        menu = webview.menu.Menu("mypi", menu_items)
        webview.start(menu=[menu], debug=False)
    except Exception:
        # Older pywebview: no menu API
        webview.start(debug=False)


if __name__ == "__main__":
    main()
