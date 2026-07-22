"""
mypi ledger — single SQLite store (crates + append-only events).
Place = place_path / place_label only (no sys/dom/mod).
"""

from __future__ import annotations

import json
import secrets
import sqlite3
import time
from pathlib import Path
from typing import Any

SCHEMA_VERSION = "2"
DEFAULT_TPS_WINDOW = 900  # 15-minute membrane windows
DEFAULT_DB = Path(__file__).resolve().parent.parent / "d" / "_LEDGER" / "mypi.sqlite"
SCHEMA_SQL = Path(__file__).resolve().parent / "schema.sql"


def new_c_uid() -> str:
    return "crate." + secrets.token_hex(8).upper()


def connect(db_path: Path | None = None) -> sqlite3.Connection:
    path = Path(db_path) if db_path else DEFAULT_DB
    path.parent.mkdir(parents=True, exist_ok=True)
    conn = sqlite3.connect(str(path))
    conn.row_factory = sqlite3.Row
    conn.execute("PRAGMA foreign_keys = ON")
    return conn


def init_db(conn: sqlite3.Connection) -> None:
    sql = SCHEMA_SQL.read_text(encoding="utf-8")
    conn.executescript(sql)
    _ensure_sys_cols(conn)
    _ensure_deleted_col(conn)
    conn.execute(
        "INSERT INTO ledger_meta(key, value) VALUES(?, ?) "
        "ON CONFLICT(key) DO UPDATE SET value=excluded.value",
        ("schema_version", SCHEMA_VERSION),
    )
    conn.execute(
        "INSERT INTO ledger_meta(key, value) VALUES(?, ?) "
        "ON CONFLICT(key) DO NOTHING",
        ("tps_window_seconds", str(DEFAULT_TPS_WINDOW)),
    )
    conn.commit()


def tps_window_seconds(conn: sqlite3.Connection) -> int:
    init_db(conn)
    row = conn.execute(
        "SELECT value FROM ledger_meta WHERE key='tps_window_seconds'"
    ).fetchone()
    w = int(row["value"]) if row else DEFAULT_TPS_WINDOW
    return w if w > 0 else DEFAULT_TPS_WINDOW


def list_tps_shelves(conn: sqlite3.Connection, limit: int = 40) -> list[sqlite3.Row]:
    init_db(conn)
    return list(
        conn.execute(
            """
            SELECT s.*,
              (SELECT COUNT(*) FROM tps_attach a WHERE a.tps_uid=s.tps_uid) AS n_crates
            FROM tps_shelves s
            ORDER BY s.window_unix DESC
            LIMIT ?
            """,
            (limit,),
        )
    )


def charlie_gravity(conn: sqlite3.Connection, limit: int = 30) -> list[sqlite3.Row]:
    init_db(conn)
    return list(
        conn.execute(
            "SELECT term, gravity, updated_at FROM thread_terms "
            "ORDER BY gravity DESC, term ASC LIMIT ?",
            (limit,),
        )
    )


def charlie_edges(conn: sqlite3.Connection, limit: int = 40) -> list[sqlite3.Row]:
    init_db(conn)
    return list(
        conn.execute(
            "SELECT * FROM thread_edges ORDER BY id DESC LIMIT ?",
            (limit,),
        )
    )


def _now() -> int:
    return int(time.time())


def _ensure_sys_cols(conn: sqlite3.Connection) -> None:
    cols = {r[1] for r in conn.execute("PRAGMA table_info(crates)")}
    for col in ("sys", "dom", "room", "mod"):
        if col not in cols:
            conn.execute(
                f"ALTER TABLE crates ADD COLUMN {col} TEXT NOT NULL DEFAULT ''"
            )
    conn.commit()


def _parse_tags(
    tags_raw: str,
    place_path: str,
    sys: str = "",
    dom: str = "",
    room: str = "",
    mod: str = "",
) -> list[str]:
    tags: list[str] = []
    raw = (tags_raw or "").replace("\n", " ").strip()
    if raw:
        for part in raw.replace(",", " ").split():
            t = part.strip().lstrip("#")
            if t and t not in tags:
                tags.append(t)
    path = (place_path or "").strip() or "/".join(
        x for x in (sys, dom, room) if x
    )
    if path:
        ptag = f"path:{path}"
        if ptag not in tags:
            tags.append(ptag)
        for seg in path.strip("/").split("/"):
            if not seg:
                continue
            at = f"@{seg}"
            if at not in tags:
                tags.append(at)
    if sys:
        t = f"sys:{sys}"
        if t not in tags:
            tags.append(t)
    if dom:
        t = f"dom:{dom}"
        if t not in tags:
            tags.append(t)
    if mod:
        t = f"mod:{mod}"
        if t not in tags:
            tags.append(t)
    return tags


def parse_charlie(tags_raw: str) -> dict[str, Any]:
    """
    Parse tags_raw into plain tags + relationship edges.

    Edge form: from*rel>to  (optionally chained with ; or newlines)
    """
    tags: list[str] = []
    edges: list[dict[str, str]] = []
    raw = (tags_raw or "").strip()
    if not raw:
        return {"tags": tags, "edges": edges}

    import re

    chunks: list[str] = []
    for p in re.split(r"[;\n]+", raw):
        p = p.strip()
        if not p:
            continue
        if "*" in p and ">" in p:
            chunks.append(p)
        else:
            for c in re.split(r"[\s,]+", p):
                c = c.strip()
                if c:
                    chunks.append(c)

    for chunk in chunks:
        chunk = chunk.strip().lstrip("#")
        if not chunk:
            continue
        m = re.match(r"^(.+?)\*(.+?)>(.+)$", chunk)
        if m:
            frm = m.group(1).strip().lower()
            rel = m.group(2).strip().lower()
            to = m.group(3).strip().lower()
            if frm and to:
                edges.append({"from": frm, "rel": rel, "to": to})
                for t in (frm, rel, to, f"{frm}*{rel}>{to}"):
                    if t and t not in tags:
                        tags.append(t)
            continue
        low = chunk.lower()
        if low and low not in tags:
            tags.append(low)
    return {"tags": tags, "edges": edges}


def charlie_write(
    conn: sqlite3.Connection,
    *,
    c_uid: str,
    tags_raw: str,
    all_tags: list[str] | None = None,
    sys: str = "",
    dom: str = "",
    room: str = "",
    mod: str = "",
    ingest_unix: int | None = None,
) -> list[dict[str, str]]:
    """
    Bump Charlie gravity for production tags + write relationship edges.

    Mirrors PHP mypi_ledger_charlie_write: every tag (including path:/@seg)
    is real Charlie material.
    """
    init_db(conn)
    ingest = int(ingest_unix if ingest_unix is not None else _now())
    parsed = parse_charlie(tags_raw)
    already: set[str] = set()

    for e in parsed["edges"]:
        conn.execute(
            """
            INSERT INTO thread_edges(
              c_uid, from_term, rel, to_term, ingest_unix, sys, dom, room, mod
            ) VALUES (?,?,?,?,?,?,?,?,?)
            """,
            (
                c_uid,
                e["from"],
                e["rel"],
                e["to"],
                ingest,
                sys or "",
                dom or "",
                room or "",
                mod or "",
            ),
        )
        for term in (
            e["from"],
            e["rel"],
            e["to"],
            f'{e["from"]}*{e["rel"]}>{e["to"]}',
        ):
            if not term or term in already:
                continue
            conn.execute(
                """
                INSERT INTO thread_terms(term, gravity, updated_at) VALUES (?,?,?)
                ON CONFLICT(term) DO UPDATE SET
                  gravity = gravity + 1,
                  updated_at = excluded.updated_at
                """,
                (term, 1, ingest),
            )
            already.add(term)

    tag_list = list(all_tags) if all_tags is not None else list(parsed["tags"])
    for t in tag_list:
        t = str(t).strip().lower()
        if not t or t in already:
            continue
        conn.execute(
            """
            INSERT INTO thread_terms(term, gravity, updated_at) VALUES (?,?,?)
            ON CONFLICT(term) DO UPDATE SET
              gravity = gravity + 1,
              updated_at = excluded.updated_at
            """,
            (t, 1, ingest),
        )
        already.add(t)
    return list(parsed["edges"])


def tps_window_unix(event_unix: int, window_seconds: int) -> int:
    """Floor event into a membrane window (same idea as PHP ledger)."""
    w = window_seconds if window_seconds > 0 else DEFAULT_TPS_WINDOW
    # negative event times still get a stable window key
    return (int(event_unix) // w) * w


def tps_uid_for_window(window_unix: int, window_seconds: int) -> str:
    # Match PHP mypi_ledger_tps_uid: "{window}.w{seconds}" (no tps. prefix)
    return f"{int(window_unix)}.w{int(window_seconds)}"


def ensure_tps_attach(
    conn: sqlite3.Connection,
    *,
    c_uid: str,
    kind: str,
    event_unix: int,
    ingest_unix: int | None = None,
) -> str:
    """Ensure TPS shelf for event window and attach crate. Returns tps_uid."""
    init_db(conn)
    w = tps_window_seconds(conn)
    ingest = int(ingest_unix if ingest_unix is not None else _now())
    window = tps_window_unix(int(event_unix), w)
    tps_uid = tps_uid_for_window(window, w)
    facets = json.dumps(
        {
            "window_unix": window,
            "window_seconds": w,
            "event_unix": int(event_unix),
        }
    )
    conn.execute(
        """
        INSERT INTO tps_shelves(
          tps_uid, window_unix, window_seconds, clock_id, facets_json, created_at
        ) VALUES (?,?,?,?,?,?)
        ON CONFLICT(tps_uid) DO NOTHING
        """,
        (tps_uid, window, w, "gaia", facets, ingest),
    )
    row = conn.execute(
        "SELECT COUNT(*) AS n FROM tps_attach WHERE tps_uid=?",
        (tps_uid,),
    ).fetchone()
    seq = int(row["n"] if row else 0)
    conn.execute(
        """
        INSERT INTO tps_attach(tps_uid, c_uid, kind, seq, attached_at)
        VALUES (?,?,?,?,?)
        ON CONFLICT(tps_uid, c_uid) DO NOTHING
        """,
        (tps_uid, c_uid, kind or "post", seq, ingest),
    )
    return tps_uid


def split_place_path(place_path: str) -> tuple[str, str, str, str]:
    """
    Split ``sys/dom/room[/mod…]`` into columns.

    First three segments → sys, dom, room. Extra segments join into mod
    (or empty). Mirrors postBASIC place as path pieces, not free-text only.
    """
    segs = [s for s in (place_path or "").strip().strip("/").split("/") if s]
    if not segs:
        return "", "", "", ""
    if len(segs) == 1:
        return segs[0], "", "", ""
    if len(segs) == 2:
        return segs[0], segs[1], "", ""
    if len(segs) == 3:
        return segs[0], segs[1], segs[2], ""
    return segs[0], segs[1], segs[2], "/".join(segs[3:])


def join_place_path(sys: str = "", dom: str = "", room: str = "", mod: str = "") -> str:
    return "/".join(x for x in (sys, dom, room, mod) if (x or "").strip())


def create_crate(
    conn: sqlite3.Connection,
    *,
    topic: str = "",
    body: str = "",
    agent: str = "user",
    tool: str = "postBASIC",
    place_path: str = "",
    place_label: str = "",
    sys: str = "",
    dom: str = "",
    room: str = "",
    mod: str = "",
    tags_raw: str = "",
    event_unix: int | None = None,
    timezone: str = "",
    actor: str = "hands",
    kind: str = "post",
    meta: dict[str, Any] | None = None,
    c_uid: str | None = None,
) -> str:
    if not (topic or body):
        raise ValueError("need topic or body")
    init_db(conn)
    _ensure_sys_cols(conn)
    c_uid = (c_uid or "").strip() or new_c_uid()
    ingest = _now()
    ev = event_unix if event_unix is not None else ingest
    place_path = (place_path or "").strip()
    sys = (sys or "").strip()
    dom = (dom or "").strip()
    room = (room or "").strip()
    mod = (mod or "").strip()
    # Prefer explicit pieces → path; else path → pieces. Never leave only a
    # free-text room with an empty sys/dom while place_path is multi-seg.
    if not place_path and (sys or dom or room or mod):
        place_path = join_place_path(sys, dom, room, mod)
    if place_path and not (sys and dom and room):
        s2, d2, r2, m2 = split_place_path(place_path)
        sys = sys or s2
        dom = dom or d2
        room = room or r2
        mod = mod or m2
    if not place_path:
        place_path = join_place_path(sys, dom, room, mod)
    tags = _parse_tags(tags_raw, place_path, sys, dom, room, mod)
    tags_json = json.dumps(tags)
    meta_json = json.dumps(meta or {})
    # provisional t_uid; replaced after ensure_tps_attach
    t_uid = f"{ev}.tps"
    conn.execute(
        """
        INSERT INTO crates(
          c_uid, kind, topic, body, agent, tool, tool_version,
          place_path, place_label, sys, dom, room, mod,
          tags_json, tags_raw,
          event_unix, ingest_unix, timezone, t_uid, meta_json,
          created_at, updated_at
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        """,
        (
            c_uid,
            kind,
            topic,
            body,
            agent,
            tool,
            1,
            place_path,
            place_label,
            sys,
            dom,
            room,
            mod,
            tags_json,
            tags_raw,
            ev,
            ingest,
            timezone,
            t_uid,
            meta_json,
            ingest,
            ingest,
        ),
    )
    for t in tags:
        conn.execute(
            "INSERT OR IGNORE INTO tag_map(c_uid, tag) VALUES(?, ?)",
            (c_uid, t),
        )
    t_uid = ensure_tps_attach(
        conn,
        c_uid=c_uid,
        kind=kind,
        event_unix=int(ev),
        ingest_unix=ingest,
    )
    conn.execute(
        "UPDATE crates SET t_uid=? WHERE c_uid=?",
        (t_uid, c_uid),
    )
    # Charlie: gravity for all production tags (path:/@seg/user) + edges
    charlie_write(
        conn,
        c_uid=c_uid,
        tags_raw=tags_raw,
        all_tags=tags,
        sys=sys,
        dom=dom,
        room=room,
        mod=mod,
        ingest_unix=ingest,
    )
    conn.execute(
        """
        INSERT INTO crate_events(
          c_uid, event_type, payload_json, actor, place_path,
          event_unix, ingest_unix, tool
        ) VALUES (?,?,?,?,?,?,?,?)
        """,
        (
            c_uid,
            "create",
            json.dumps(
                {
                    "topic": topic,
                    "body": body,
                    "tags": tags,
                    "place_path": place_path,
                    "t_uid": t_uid,
                }
            ),
            actor,
            place_path,
            ev,
            ingest,
            tool,
        ),
    )
    conn.commit()
    return c_uid


def add_tag(
    conn: sqlite3.Connection,
    c_uid: str,
    tag: str,
    *,
    actor: str = "hands",
    place_path: str = "",
    tool: str = "mypi-tui",
) -> None:
    tag = tag.strip().lstrip("#")
    if not tag:
        raise ValueError("empty tag")
    init_db(conn)
    row = conn.execute("SELECT tags_json, tags_raw FROM crates WHERE c_uid=?", (c_uid,)).fetchone()
    if not row:
        raise KeyError(c_uid)
    tags = json.loads(row["tags_json"] or "[]")
    if tag in tags:
        return
    tags.append(tag)
    ingest = _now()
    raw = (row["tags_raw"] or "").strip()
    new_raw = f"{raw} {tag}".strip()
    conn.execute(
        "UPDATE crates SET tags_json=?, tags_raw=?, updated_at=? WHERE c_uid=?",
        (json.dumps(tags), new_raw, ingest, c_uid),
    )
    conn.execute(
        "INSERT OR IGNORE INTO tag_map(c_uid, tag) VALUES(?, ?)",
        (c_uid, tag),
    )
    conn.execute(
        """
        INSERT INTO crate_events(
          c_uid, event_type, payload_json, actor, place_path,
          event_unix, ingest_unix, tool
        ) VALUES (?,?,?,?,?,?,?,?)
        """,
        (
            c_uid,
            "tag_add",
            json.dumps({"tag": tag}),
            actor,
            place_path,
            ingest,
            ingest,
            tool,
        ),
    )
    conn.commit()


def set_body(
    conn: sqlite3.Connection,
    c_uid: str,
    body: str,
    *,
    actor: str = "hands",
    place_path: str = "",
    tool: str = "mypi-tui",
) -> None:
    init_db(conn)
    row = conn.execute("SELECT body FROM crates WHERE c_uid=?", (c_uid,)).fetchone()
    if not row:
        raise KeyError(c_uid)
    old = row["body"]
    ingest = _now()
    conn.execute(
        "UPDATE crates SET body=?, updated_at=? WHERE c_uid=?",
        (body, ingest, c_uid),
    )
    conn.execute(
        """
        INSERT INTO crate_events(
          c_uid, event_type, payload_json, actor, place_path,
          event_unix, ingest_unix, tool
        ) VALUES (?,?,?,?,?,?,?,?)
        """,
        (
            c_uid,
            "set_body",
            json.dumps({"old": old, "new": body}),
            actor,
            place_path,
            ingest,
            ingest,
            tool,
        ),
    )
    conn.commit()


def list_crates(
    conn: sqlite3.Connection,
    *,
    place_path: str | None = None,
    tag: str | None = None,
    limit: int = 50,
    include_deleted: bool = False,
) -> list[sqlite3.Row]:
    init_db(conn)
    _ensure_deleted_col(conn)
    alive = "" if include_deleted else " AND (c.deleted_at IS NULL OR c.deleted_at=0)"
    alive_plain = "" if include_deleted else " AND (deleted_at IS NULL OR deleted_at=0)"
    if tag:
        q = f"""
        SELECT c.* FROM crates c
        JOIN tag_map t ON t.c_uid = c.c_uid
        WHERE t.tag = ?
        {alive}
        """
        args: list[Any] = [tag]
        if place_path:
            q += " AND c.place_path = ?"
            args.append(place_path)
        q += " ORDER BY c.ingest_unix DESC LIMIT ?"
        args.append(limit)
        return list(conn.execute(q, args))
    if place_path:
        return list(
            conn.execute(
                f"SELECT * FROM crates WHERE place_path=? {alive_plain} "
                "ORDER BY ingest_unix DESC LIMIT ?",
                (place_path, limit),
            )
        )
    return list(
        conn.execute(
            f"SELECT * FROM crates WHERE 1=1 {alive_plain} "
            "ORDER BY ingest_unix DESC LIMIT ?",
            (limit,),
        )
    )


def get_crate(conn: sqlite3.Connection, c_uid: str) -> sqlite3.Row | None:
    init_db(conn)
    return conn.execute("SELECT * FROM crates WHERE c_uid=?", (c_uid,)).fetchone()


def history(conn: sqlite3.Connection, c_uid: str) -> list[sqlite3.Row]:
    init_db(conn)
    return list(
        conn.execute(
            "SELECT * FROM crate_events WHERE c_uid=? ORDER BY id ASC",
            (c_uid,),
        )
    )


def _ensure_deleted_col(conn: sqlite3.Connection) -> None:
    cols = {r[1] for r in conn.execute("PRAGMA table_info(crates)")}
    if "deleted_at" not in cols:
        conn.execute("ALTER TABLE crates ADD COLUMN deleted_at INTEGER")
    conn.execute(
        """
        CREATE TABLE IF NOT EXISTS deleted_log (
          id INTEGER PRIMARY KEY AUTOINCREMENT,
          c_uid TEXT NOT NULL,
          snapshot_json TEXT NOT NULL,
          deleted_at INTEGER NOT NULL,
          actor TEXT NOT NULL DEFAULT '',
          hard INTEGER NOT NULL DEFAULT 0
        )
        """
    )
    conn.commit()


def soft_delete(
    conn: sqlite3.Connection,
    c_uid: str,
    *,
    actor: str = "hands",
    tool: str = "mypi-tui",
) -> None:
    init_db(conn)
    _ensure_deleted_col(conn)
    row = get_crate(conn, c_uid)
    if not row:
        raise KeyError(c_uid)
    if row["deleted_at"]:
        return
    now = _now()
    conn.execute(
        "UPDATE crates SET deleted_at=?, updated_at=? WHERE c_uid=?",
        (now, now, c_uid),
    )
    conn.execute(
        """
        INSERT INTO crate_events(
          c_uid, event_type, payload_json, actor, place_path,
          event_unix, ingest_unix, tool
        ) VALUES (?,?,?,?,?,?,?,?)
        """,
        (
            c_uid,
            "soft_delete",
            json.dumps({"topic": row["topic"]}),
            actor,
            row["place_path"] or "",
            now,
            now,
            tool,
        ),
    )
    conn.execute(
        """
        INSERT INTO deleted_log(c_uid, snapshot_json, deleted_at, actor, hard)
        VALUES (?,?,?,?,0)
        """,
        (c_uid, json.dumps(dict(row)), now, actor),
    )
    conn.commit()


def hard_delete(
    conn: sqlite3.Connection,
    c_uid: str,
    *,
    actor: str = "hands",
    tool: str = "mypi-tui",
) -> None:
    """Remove one crate only. Never the whole world."""
    init_db(conn)
    _ensure_deleted_col(conn)
    row = get_crate(conn, c_uid)
    if not row:
        raise KeyError(c_uid)
    now = _now()
    conn.execute(
        """
        INSERT INTO deleted_log(c_uid, snapshot_json, deleted_at, actor, hard)
        VALUES (?,?,?,?,1)
        """,
        (c_uid, json.dumps(dict(row)), now, actor),
    )
    conn.execute("DELETE FROM tag_map WHERE c_uid=?", (c_uid,))
    conn.execute("DELETE FROM crate_events WHERE c_uid=?", (c_uid,))
    conn.execute("DELETE FROM crates WHERE c_uid=?", (c_uid,))
    conn.commit()


def stats(conn: sqlite3.Connection) -> dict[str, Any]:
    init_db(conn)
    _ensure_deleted_col(conn)
    n = conn.execute(
        "SELECT COUNT(*) AS n FROM crates WHERE deleted_at IS NULL OR deleted_at=0"
    ).fetchone()["n"]
    e = conn.execute("SELECT COUNT(*) AS n FROM crate_events").fetchone()["n"]
    ver = conn.execute(
        "SELECT value FROM ledger_meta WHERE key='schema_version'"
    ).fetchone()
    return {
        "crates": n,
        "events": e,
        "schema_version": ver["value"] if ver else "?",
        "db": str(DEFAULT_DB),
    }
