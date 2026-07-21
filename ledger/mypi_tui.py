"""
mypi-tui — trust the ledger.

  cd C:\\Builds\\my-pocket-internet\\ledger
  python mypi_tui.py

Or: python -m ledger.mypi_tui from mypi root if packaged later.
"""

from __future__ import annotations

import json
import sys
from pathlib import Path

# allow running as script
sys.path.insert(0, str(Path(__file__).resolve().parent))

from mypi_ledger import (  # noqa: E402
    DEFAULT_DB,
    add_tag,
    connect,
    create_crate,
    get_crate,
    hard_delete,
    history,
    init_db,
    list_crates,
    set_body,
    soft_delete,
    stats,
)

try:
    from textual import on
    from textual.app import App, ComposeResult
    from textual.binding import Binding
    from textual.containers import Horizontal, Vertical
    from textual.widgets import Button, DataTable, Footer, Header, Input, Label, Static
except ImportError:
    print("Need textual: pip install textual")
    sys.exit(1)


class MypiTui(App[None]):
    TITLE = "mypi ledger"
    CSS = """
    Screen { background: #0c1210; color: #b8e0c8; }
    #sidebar { width: 28; border-right: tall #2a4a38; padding: 1; }
    #main { padding: 1 2; }
    #detail { height: 1fr; border: tall #2a4a38; padding: 1; }
    Button { margin: 0 0 1 0; width: 100%; }
    DataTable { height: 14; }
    .muted { color: #5a8a6a; }
    #status { color: #7ab890; margin-top: 1; }
    """
    BINDINGS = [
        Binding("q", "quit", "Quit"),
        Binding("r", "refresh", "Refresh"),
    ]

    def __init__(self) -> None:
        super().__init__()
        self.conn = connect()
        init_db(self.conn)
        self._selected: str | None = None

    def compose(self) -> ComposeResult:
        yield Header()
        with Horizontal():
            with Vertical(id="sidebar"):
                yield Label("mypi ledger")
                yield Static("Trust viewer — no sys/dom/mod", classes="muted")
                yield Button("Refresh list", id="btn-refresh")
                yield Button("Add demo crate", id="btn-demo")
                yield Button("Add tag 'trusted'", id="btn-tag")
                yield Button("Edit body (append note)", id="btn-edit")
                yield Button("Soft-delete (crate remains, hidden)", id="btn-del")
                yield Button("NUKE crate (double-post / poison only)", id="btn-hard")
                yield Static("", id="status")
            with Vertical(id="main"):
                yield Label("Crates (newest first)")
                yield DataTable(id="table")
                yield Label("Detail + history")
                yield Static("(select a row)", id="detail")
        yield Footer()

    def on_mount(self) -> None:
        table = self.query_one("#table", DataTable)
        table.add_columns("c_uid", "place", "topic", "ingest")
        table.cursor_type = "row"
        self.action_refresh()

    def action_refresh(self) -> None:
        table = self.query_one("#table", DataTable)
        table.clear()
        rows = list_crates(self.conn, limit=100)
        for r in rows:
            topic = (r["topic"] or r["body"] or "")[:40]
            table.add_row(
                r["c_uid"],
                r["place_path"] or "—",
                topic.replace("\n", " "),
                str(r["ingest_unix"]),
                key=r["c_uid"],
            )
        st = stats(self.conn)
        self.query_one("#status", Static).update(
            f"crates={st['crates']} events={st['events']} v{st['schema_version']}\n{st['db']}"
        )
        if self._selected:
            self._show(self._selected)

    def _show(self, c_uid: str) -> None:
        self._selected = c_uid
        row = get_crate(self.conn, c_uid)
        detail = self.query_one("#detail", Static)
        if not row:
            detail.update("missing")
            return
        tags = json.loads(row["tags_json"] or "[]")
        lines = [
            f"c_uid:     {row['c_uid']}",
            f"kind:      {row['kind']}",
            f"tool:      {row['tool']}",
            f"sys/dom/room/mod: {row['sys'] if 'sys' in row.keys() else ''}/"
            f"{row['dom'] if 'dom' in row.keys() else ''}/"
            f"{row['room'] if 'room' in row.keys() else ''}/"
            f"{row['mod'] if 'mod' in row.keys() else ''}",
            f"path:      {row['place_path'] or '(none)'}",
            f"label:     {row['place_label'] or ''}",
            f"topic:     {row['topic']}",
            f"body:      {row['body'][:500]}",
            f"tags:      {', '.join(tags)}",
            f"event_unix:{row['event_unix']}  ingest:{row['ingest_unix']}",
            f"t_uid:     {row['t_uid']}",
            "",
            "— history —",
        ]
        for ev in history(self.conn, c_uid):
            payload = ev["payload_json"]
            if len(payload) > 120:
                payload = payload[:117] + "..."
            lines.append(
                f"  #{ev['id']} {ev['event_type']} @{ev['ingest_unix']} "
                f"[{ev['actor']}] {payload}"
            )
        detail.update("\n".join(lines))

    @on(DataTable.RowSelected)
    def row_selected(self, event: DataTable.RowSelected) -> None:
        if event.row_key:
            self._show(str(event.row_key.value))

    @on(Button.Pressed, "#btn-refresh")
    def refresh_btn(self) -> None:
        self.action_refresh()

    @on(Button.Pressed, "#btn-demo")
    def demo(self) -> None:
        create_crate(
            self.conn,
            topic="demo trust post",
            body="If you can see history after tagging, the ledger works.",
            sys="workshop",
            dom="trust",
            room="desk",
            mod="hands",
            place_label="Trust desk",
            tags_raw="demo ledger",
            actor="mypi-tui",
            tool="mypi-tui",
        )
        self.action_refresh()

    @on(Button.Pressed, "#btn-tag")
    def tag(self) -> None:
        if not self._selected:
            self.query_one("#status", Static).update("select a crate first")
            return
        add_tag(self.conn, self._selected, "trusted", actor="mypi-tui", tool="mypi-tui")
        self.action_refresh()

    @on(Button.Pressed, "#btn-edit")
    def edit(self) -> None:
        if not self._selected:
            self.query_one("#status", Static).update("select a crate first")
            return
        row = get_crate(self.conn, self._selected)
        if not row:
            return
        new_body = (row["body"] or "") + "\n[edit] hands touched this in mypi-tui"
        set_body(self.conn, self._selected, new_body, actor="mypi-tui", tool="mypi-tui")
        self.action_refresh()

    @on(Button.Pressed, "#btn-del")
    def del_soft(self) -> None:
        if not self._selected:
            self.query_one("#status", Static).update("select a crate first")
            return
        soft_delete(self.conn, self._selected, actor="mypi-tui")
        self._selected = None
        self.action_refresh()

    @on(Button.Pressed, "#btn-hard")
    def del_hard(self) -> None:
        if not self._selected:
            self.query_one("#status", Static).update("select a crate first")
            return
        hard_delete(self.conn, self._selected, actor="mypi-tui")
        self._selected = None
        self.action_refresh()


def main() -> None:
    print(f"ledger db: {DEFAULT_DB}")
    MypiTui().run()


if __name__ == "__main__":
    main()
