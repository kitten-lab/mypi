# Tools → ledger store (v6)

JSON room-chest tools backed up under `t/tools/-v3/*-json/`.  
Live root tools write **`d/_LEDGER/mypi.sqlite`** via `mypi_ledger_create_post` (same rail as postBASIC).

## Kinds

| Tool | `kind` | Identity of a “row” | Notes |
|------|--------|---------------------|--------|
| postBASIC | `post` | headline / body | Charlie tags + TPS |
| cuBOOK | `guestcu` | guest line | `agent` = name, `body` = greeting |
| soprBASIC | `soper` | fragment | `topic` = section, `meta.section_slug` for groups |
| chatBOX | `chat` | **one line in a session** | see below |
| ledgerREPORT | — | reads all | filters by kind / place |

## chatBOX sessions (live hangout)

Most juvenile live tool — not a single flat log forever.

- Default session id: **`live`** (per sky place: sys/dom/room).
- Optional POST `chat_session` (slug) + `chat_session_label` (title).
- Each POST = **one line crate** (not “the whole session in topic”):

| Field | Meaning |
|-------|---------|
| `agent` | **Who** said it (nick). Only place the user lives. |
| `body` | **What** they said |
| `topic` | **Hangout title** (`session_label`) for generic crate lists — *not* the speaker |
| `meta.session` | Session id slug (**payload**, not a tag) |
| `meta.session_label` | Same human title |
| `meta.speaker` | Echo of nick (redundant safety) |
| `tags` / `tags_raw` | **Charlie:** auto **`@nick`** from speaker + optional user threading. Not session. |

### Cosmology rule (all tools)

| Bucket | What belongs |
|--------|----------------|
| **Columns + `meta` + body/topic/agent** | This crate’s unique payload (including session, tool-private structure) |
| **`tags` / Charlie** | Master-world threading — user keywords, edges, **and** auto place tags (`@room`, `path:…`, `sys:…`) |

Do **not** smuggle **tool-private** keys into tags (e.g. chat session id). Those live in **`meta`**.  
List filters use **columns** + **`meta`** for tool structure; Charlie still *sees* place tags as real gravity.

**Auto place tags are production Charlie material.**  
`mypi_ledger_parse_tags` appends `path:…`, `@seg`, `sys:`, `dom:`, `mod:` onto every crate’s tag set when place is known. Those go into `tags_json` / `tag_map` **and** into Charlie **gravity** (no longer skipped for `:` / `@`).  

Still **not** tags: tool-private payload (e.g. chat `meta.session`) — that stays in `meta`, not fake `session:…` tags.

**Edges** come from relationship language `a*connector>c` in user `tags_raw`.  
For each edge, **tags + gravity** include all four pieces: **`a`**, **`connector`**, **`c`**, and the full chain `a*connector>c` (connector was previously omitted as its own term).

- **ChatRoom** lists with `order=asc` filtered by **`meta.session`**.
- Session switcher: `mypi_ledger_chat_sessions()`.
- Older test rows may still have `chat` / `session:…` in tags or synthetic topics; new chat writes do not.

## List API extras

`mypi_ledger_list` accepts: `tool`, `session` (**json_extract meta.session**, not a tag), `order` (`asc`|`desc`), `mod`.

## Backup restore

Old JSON writers: `t/tools/-v3/cuBOOK-json`, `chatBOX-json`, `soprBASIC-json`.
