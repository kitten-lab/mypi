# How storage works + how to look at it

## Where data lives

| What | Path |
|------|------|
| **SQLite ledger** | `C:\Builds\my-pocket-internet\d\_LEDGER\mypi.sqlite` |
| Same via junction | `C:\xampp\htdocs\my-pocket-internet\d\_LEDGER\mypi.sqlite` |

**Not** written anymore by new postBASIC: `d/starline/news-headlines.post.json` style room slips.

## What a row holds (crate)

- `c_uid` — stable id (`crate.…`)
- `kind` — `post` (later fragment/chat/…)
- `topic` / `body`
- **`sys` / `dom` / `room` / `mod`** — where posted + who (modifier)
- `place_path` — `sys/dom/room` joined (query helper)
- `tags_json` / `tags_raw` — user tags + auto `@segment`, `sys:`, `dom:`, `mod:`
- `event_unix` / `ingest_unix` / `t_uid` — time buckets
- `tool` — `postBASIC`

## History

Table **`crate_events`**: every create (and later edits) is append-only with timestamp.

## How to look

### 1. On the Surface (Skyline News)

Pocket browser or:

`http://starline/news/headlines`

- Form = MakePost  
- List below = ledger rows for this SYS/DOM  

### 2. mypi-tui (trust desk)

```bat
cd C:\Builds\my-pocket-internet\ledger
python mypi_tui.py
```

Refresh list, select a crate, see tags + history.  
**Add tag / Edit body** also leave events (from TUI).

### 3. SQLite CLI (if installed)

```bat
sqlite3 C:\Builds\my-pocket-internet\d\_LEDGER\mypi.sqlite
```

```sql
SELECT c_uid, sys, dom, room, mod, topic, ingest_unix FROM crates ORDER BY ingest_unix DESC LIMIT 20;
SELECT * FROM crate_events ORDER BY id DESC LIMIT 20;
SELECT tag, COUNT(*) FROM tag_map GROUP BY tag;
```

### 4. DB Browser for SQLite

Open `mypi.sqlite` in [DB Browser for SQLite](https://sqlitebrowser.org/) — free GUI.

### 5. One-file backup

Copy `d/_LEDGER/mypi.sqlite` somewhere safe. That **is** your thoughts DB for postBASIC-class cargo.

## Flow

```text
Skyline / news / headlines  (MOD system)
  MakePost  →  Ledger.php  →  mypi.sqlite
  SoperView →  SELECT from same file
  mypi-tui  →  same file
```

## Skyline News coordinates

| Role | Value |
|------|--------|
| SYS | starline |
| DOM | news |
| ROOM | headlines |
| MOD | system (System Voice) |
