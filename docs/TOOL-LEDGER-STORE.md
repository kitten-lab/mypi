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
- Each POST = one crate:
  - `kind=chat`, `tool=chatBOX`
  - `agent` = username
  - `body` = message
  - `meta.session` / `meta.session_label` / `meta.live=true`
  - tag `session:{id}` for cheap grep
- **ChatRoom** lists with `order=asc` (oldest → newest) filtered by `meta.session`.
- Session switcher built from `mypi_ledger_chat_sessions()`.

## List API extras

`mypi_ledger_list` accepts: `tool`, `session` (json_extract meta), `order` (`asc`|`desc`), `mod`.

## Backup restore

Old JSON writers: `t/tools/-v3/cuBOOK-json`, `chatBOX-json`, `soprBASIC-json`.
