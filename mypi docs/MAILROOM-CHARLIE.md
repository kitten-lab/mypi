# Master Mailroom · Charlie · Timbers

**Status:** BUILT v0 — sort floor + timberBay  
**SYS:** `mailroom` (platform, **not** a terminal)  
**URL:** `/mailroom/floor/sort`  
**Network:** Chester (C) — Charlie works for no one else  

---

## Law

- Atoms on the floor = **timbers** (`c_uid`)  
- Charlie slings **red thread** (Charlie tags + `*rel>` edges)  
- Starline may *show* mail movement; **does not host** this bay  
- Early starline/chester|charlie doors = test stubs → real lab in www/lab  
- **No bare timber on purpose** — queue **Bare** first  

---

## UX (v0)

| | |
|--|--|
| Queues | All · Bare · Terms · Wired |
| Sort | newest · event · tags · edges · kind · place |
| Filter | kind · agent · place · search |
| Chips | plain tags + edge chips `from*rel>to` |
| Tag+ / Set raw | append or replace `tags_raw` |

**Surface ownership (important):**  
| Layer | Owns |
|-------|------|
| **`a/mailroom/asSys`** | Brand bar, rail **chrome** (MAIL/ROOM, CHAR/LIE), three-zone frame |
| **`timberBay` Rail** | Queues + filters only (no facility slogans) |
| **`timberBay` Desk** | Yard rows + manage panel |

Edit facility labels in **shell.php**, not the tool.  
`getTool(..., 'Rail')` injects into shell rail slot; Desk into main slot.

---

## Related

- Codex: Charlie · Master Mailroom · Timbers  
- Ledger TUI remains back alley  
- BarB set aside (tarot terminal later)  
