# Daily Inventory · inventOry / dailyLog tool

**Status:** PLAN / NOTE — **after** Sam’s FILES bag (done) and before or beside multi-terminal  
**Source:** `D:\_Chester's Imports\Terminal IO\USERS\SDK808\Daily Inventory\`  
**Example:** `250916 - Tuesday Sep 16, 2025.md`  
**Hands:** Sam’s **document** files are in; these are a **different type** — day shells with per-entry inserts.

---

## 1. What it is (not fileKeeper)

| fileKeeper (SAM’S FILES) | Daily Inventory |
|--------------------------|-----------------|
| Named documents, folders, revisions | **One shell per calendar day** |
| Edit whole body as a file | **Insert posts during the day** into sections |
| Stem/rev document lineage | Day **opens → entries accumulate → day closes** |
| Topic = title | Topic/face = **date + weekday** |

Closest cousins: Obsidian daily notes + a **structured inserter** (sections, context, tags, chronokey per entry).  
**Verdict:** yes — this is **technically a new tool** (working name: **`dailyLog`** / **`inventOry`** / **invent-0rium** — Chester renames; see §1b).  
Do **not** force these into fileKeeper folders long-term; import path can stage them, practice home is the log tool.

### 1a. Purpose (why it exists)

**Capture what she is reading / noticing while she is importing.**

Not a separate diary hobby — a **parallel channel** during FILES import, log-yard work, Glass bleed, music, omens:  
“I am in the bag right now and **this** just hit.”  

So inventOry is the **awareness inserter** beside the work, not a second wiki to maintain.

### 1b. Name drift (Chester has issues)

Surface name **changes on purpose / by glitch**: daily inventory · invent-ory · invent-0rium · daily invent-ory · etc.  
**Internal tool id** stays stable (`dailyLog` or `inventOry`).  
**Display title** can be whimsical / rotated; do not hardcode one string as law in UI chrome only.

### 1c. Three ways to insert (all first-class)

| path | when |
|------|------|
| **1. Hotkey** | Anywhere in the house (esp. while in FILES / import): keyboard launches **quick insert** → today’s open day (or open-or-create today) → type / section / tags → stamp chronokey → done. **Ideal default.** |
| **2. invent-0rium desk** | Go to the day tool on purpose: pick day, browse sections, insert, close day, read the report. |
| **3. In the log itself** | Day view is editable structure: **add a section** directly on the day, or append an entry under a section without a separate modal if she is already “in” the log. |

Hotkey = capture while importing.  
Desk = inhabit the day.  
In-log = grow the skeleton (new section headers, free structure) without fighting the template.

---

## 2. Observed format (Sam / SDK808)

### Filename
```text
YYMMDD - Weekday Mon DD, YYYY.md
e.g. 250916 - Tuesday Sep 16, 2025.md
```

### Frontmatter (Obsidian)
- `sticker`, `banner`, `banner_y`, vault `tags`, misc fields  
- House can keep banner/sticker as **meta** or ignore until media lands.

### Day header
```text
# daily inventory
CHONO-KEY / CHRONOKEY  (house face time code)
STANDARD DATE
LAST UPDATED
```
Optional day-level checklist (`- [ ] Capture songs before sleep`).

### Sections (variable per day; recurring patterns)
Examples from 250916:

- `strange happenings` → **omens & signs**
- **whispers of casey vale**
- **agent k. logs** / morning thoughts
- **random thoughts**
- **closing reflection** (end-of-day seal)
- **notable music** (listens with links / timestamps)

### Entry atom (repeats inside sections)
```text
#### {title or omen line}
@ {wall clock time}

{body}

CONTEXT: **…**
TAGGED: #a #b
CHRONOKEY:: | …   (often in <sup>)
---
```

Music entries are looser: track heading + spotify link + wall time.

### Day lifecycle (narrative)
1. Day file exists / opens  
2. Entries **inserted** through the day (not one rewrite of a novel)  
3. **Closing reflection** (+ music rollup)  
4. Day is “closed” (report complete) — further edits are exceptional

---

## 3. Ledger model — **log + leaves** (locked)

Same grain as **chatBOX** and **glass logs**: a day is a **log**; each insert is a **leaf row** — real crates, not a JSON array stuffed inside one container.

### Scale
| grain | kind (suggested) | what |
|-------|------------------|------|
| **Day shell (log)** | `dailylog` | One crate per `YYYY-MM-DD` + agent — opens/closes, sections list, checklist, banner meta |
| **Entry (leaf)** | `dailylog_entry` | **One crate per insert** — `parent_c_uid` / `stem` ties to day; body + context + tags + event_unix + chronokey |

> [!NOTE] Hands
> Consider the current system as it is now — the daily log would be a **log**, and each input a **leaf** of it, as per chatbox and the glass logs. **Not** container-inner JSON blob — **actual rows per insert.**

> [!TIP] Agent
> **Locked:** no `entries[]` blob as source of truth. Day crate = log header / rollup face; each invent insert = **new leaf crate** under that day. Dual-write to Skyline report = **another leaf/copy** with `source_c_uid` → invent leaf (see REPORT-SERVICES-FROM-TERMINAL). Optional: day body rebuilt as md export for reading; leaves remain canonical for Charlie/TPS/delete.

### Place
```text
sys: terminal   (or later IO-specific)
dom: io
room: inventory   # or dailylog
agent: sdk808 / sam
mod:  (empty or agent — not report character mods)
```

### Day meta (sketch — log shell only)
```json
{
  "day": "2025-09-16",
  "weekday": "Tuesday",
  "chronokey": "20253.38-09-2Tu-16AM.…",
  "standard_date": "09-16-2025",
  "last_updated": "02:36",
  "closed": true,
  "source_path": "…/250916 - Tuesday Sep 16, 2025.md",
  "banner": "…",
  "checklist": ["Capture songs before sleep"],
  "sections": ["omens & signs", "whispers of casey vale", "…"]
}
```

### Entry leaf (crate fields + meta)
```json
{
  "kind": "dailylog_entry",
  "parent_c_uid": "<day log c_uid>",
  "topic": "5:02:22 on the clock",
  "body": "…",
  "event_unix": 1758056547,
  "tags_raw": "signs/angel-numbers",
  "meta": {
    "section": "omens & signs",
    "wall_time": "05:02 PM",
    "context": "thinking about CASEY",
    "chronokey": "…",
    "entry_type": "omen|import|integrate|crate|react|free"
  }
}
```

---

## 4. Tool surfaces (v0 → v1)

### v0 — Tool spine (not “must bulk-import vault”)
- Open/list **days** (log crates); **insert leaf** with **event time** (backdate allowed).  
- View day = log header + ordered leaves.  
- Vault `Daily Inventory/*.md`: **optional** bulk parse — hands can **manually re-enter** historical days if backdates work. Bulk import only if manual is too annoying.

> [!NOTE] Hands
> Did **not** expect you to import the existing files — you can, but I can also **manually reimport** as long as the tool allows **backdates**. If too annoying, then yes help importing.

> [!TIP] Agent
> **v0 priority:** insert + **settable event_unix / day** (backdate). Bulk vault import = **optional help**, not a blocker. If we bulk later: day log + one leaf per parsed entry (not one fat body-only day).

### v1 — invent-0rium desk + insert paths
- **Index:** calendar or list of days (open / closed badge).  
- **Day view:** sections as headers; **leaves** as cards; **add section** on the day.  
- **Insert (desk):** section → title, body, context, tags → **new leaf crate** + chronokey.  
- **Insert (hotkey):** terminal-scoped shortcut → form → today’s day (or chosen backdate).  
- **Insert (in-log):** add section or leaf inline.  
- **Close day:** closing reflection leaf or day meta; `closed: true` (soft lock).  
- **Skyline dual-write:** on form — copy leaf to report bucket (see report services plan).  
- **Music:** optional notable-music leaf type.

### Hotkey sketch (v1)
- e.g. `Ctrl+Shift+I` or `Ctrl+.` — **I**nvent / insert (bindings TBD).  
- Overlay: section, body, context/tags, optional **whom** (Oman / hymns).  
- Default **today**; backdate if expanded.  
- Works while FILES is open — that’s the point.

### Non-goals v1
- Full Obsidian sticker/banner chrome  
- Auto angel-number detection  
- Merging with chatBOX sessions (different grain)  
- Forcing one display name forever  
- Requiring bulk vault import before the tool is usable  

---

## 5. Relation to other work

| work | relation |
|------|----------|
| **SAM’S FILES** | Done for document type; inventories are **next Sam type** |
| **fileKeeper** | Do not overload; link “open in files” only if someone copies a day out |
| **Charlie** | **Per-leaf** tags + `#signs/…`; tag-adder later helps |
| **Media** | Banners / invoice.png / embeds → [MEDIA-AND-DIAGRAM-BOARDS.md](./MEDIA-AND-DIAGRAM-BOARDS.md) |
| **sendIT / AB·JX** | Inventories stay **IO / Sam life-log** unless a day is truly AB casework |
| **MUSIC** | “notable music” leaves may twin to music leaves later |
| **Report services** | Dual-write copy from invent **leaf** → `skyline/services/{omens,hymns}` ([REPORT-SERVICES-FROM-TERMINAL.md](./REPORT-SERVICES-FROM-TERMINAL.md)) |

---

## 6. Sequence

```text
[done]   Sam document files → fileKeeper
[now]    Plan locked (log+leaves, backdates, optional vault reimport)
[next]   inventOry: day log + leaf insert + backdate event time
         + dual-write to Skyline buckets (Oman / hymns)
[opt]    Bulk parse vault Daily Inventory if manual backdate grind sucks
[later]  Charlie on entry leaves; music cross-links; media banners
```

---

## 7. One-line law

> **A day is a log that opens; each insert is a leaf row — like chat and glass, not a JSON bag.**  
> **She inserts while importing — hotkey, invent-0rium, or in the log itself.**  
> **Backdate allowed; bulk vault import optional.**

---

## 8. Templater suite (FTP templates)

**Path:** `D:\_Chester's Imports\Terminal IO\FTP\_ASSETS\templates\`

These are the **insert engines** Sam used — inventOry desk should re-embody them as form actions, not re-require Obsidian.

| file | role |
|------|------|
| **`log template.md`** | **Day shell** — invent-ory open: title, CHONO-KEY, LAST UPDATED, section stubs (`INCOMING EVENTS` / `FINAL DAILY RECORD` / footnotes). Prompt: weekday date + time. |
| **`injector-chronokey.md`** | Tiny **time face** only: `<% tp.date.now("YQ:WW-MM:edd-DDA.XN") %>` → house chronokey grammar. |
| **`BLOCK- import1.md`** | Insert block: `>>> #import` + `#timelog` |
| **`BLOCK- integrate1.md`** | Insert block: `>>> #integrate` + `#timelog` |
| **`BLOCK- crate1.md`** | Insert block: `>>> #crated` + `#timelog` |
| **`BLOCK- comment-block.md`** | Comment chain: `#REACT/…`, `#SDK808/posted`, timelog with date/time |
| **`CRE8SEEDS.md`** | **Packing-slip / crate seed** form (UNISELEPH LOGIcISTICS) — destination terminal, handler, CID, contents, article seed. Different tool face (yard/crate) but same ritual language. |
| **`receive email.md` / `send email.md`** | Mail theater: TO/FROM/SUBJECT/BODY + chronokey + `#you-got-mail` / `#you-sent-mail` — maps to terminal **E-Mail** room, not inventOry proper. |
| **`templates.md`** | Empty sticker stub |

### Day shell excerpt (`log template.md`)

```markdown
# daily invent-ory
###### for {{DATE:dddd MMM DD, YYYY}}
CHONO-KEY::  {{DATE:YQ.WW-MM-edd-DDA.XN}}
LAST UPDATED::  {{time}}

## INCOMING EVENTS
<> capture your important awarenesses
###### TRACK YOUR TRACKS

## FINAL DAILY RECORD
<> make some meaning of the import-anted bits!
```

### inventOry tool mapping

| Templater action | Tool action |
|------------------|-------------|
| New note from **log template** | **Open day** |
| Paste **BLOCK-*** / comment | **Insert entry** (type: import / integrate / crate / react) |
| **injector-chronokey** | Auto-stamp `event_unix` + face chronokey on every insert |
| **CRE8SEEDS** | Optional “seed crate” from a day (or separate crate tool) |
| send/receive **email** | IO mail surface (later), not day body |

Entry atoms in lived days (CONTEXT / TAGGED / #### title @ time) may have been **manual or another snippet** — still the **canonical entry schema** for v1 insert form even if not a separate file in this folder.

### Chronokey grammar (preserve)

Obsidian: `YQ:WW-MM:edd-DDA.XN` / `YQ.WW-MM-edd-DDA.XN`  
House should generate the same **face** when inserting, store raw `event_unix` beside it.

---

## Related

- Example path: `D:\_Chester's Imports\Terminal IO\USERS\SDK808\Daily Inventory\`  
- Templates: `D:\_Chester's Imports\Terminal IO\FTP\_ASSETS\templates\`  
- [SENDIT-AND-TERMINAL-SHELVES.md](./SENDIT-AND-TERMINAL-SHELVES.md)  
- [MEDIA-AND-DIAGRAM-BOARDS.md](./MEDIA-AND-DIAGRAM-BOARDS.md)  
- [TOOL-LEDGER-STORE.md](./TOOL-LEDGER-STORE.md)  
