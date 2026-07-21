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

SCHEMA_VERSION = "1"
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
    conn.execute(
        "INSERT INTO ledger_meta(key, value) VALUES(?, ?) "
        "ON CONFLICT(key) DO UPDATE SET value=excluded.value",
        ("schema_version", SCHEMA_VERSION),
    )
    conn.commit()


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
    path = place_path or "/".join(x for x in (sys, dom, room) if x)
    if path:
        tags.append(f"path:{path}")
        for seg in path.strip("/").split("/"):
            if seg and f"@{seg}" not in tags:
                tags.append(f"@{seg}")
    if sys:
        tags.append(f"sys:{sys}")
    if dom:
        tags.append(f"dom:{dom}")
    if mod:
        tags.append(f"mod:{mod}")
    return tags


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
) -> str:
    if not (topic or body):
        raise ValueError("need topic or body")
    init_db(conn)
    _ensure_sys_cols(conn)
    c_uid = new_c_uid()
    ingest = _now()
    ev = event_unix if event_unix is not None else ingest
    t_uid = f"{ev}.tps"
    if not place_path:
        place_path = "/".join(x for x in (sys, dom, room) if x)
    tags = _parse_tags(tags_raw, place_path, sys, dom, room, mod)
    tags_json = json.dumps(tags)
    meta_json = json.dumps(meta or {})
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
