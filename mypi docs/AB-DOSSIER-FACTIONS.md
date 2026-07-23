# Terminal AB · Dossier (factions & members)

**Status:** CONCEPT / PLAN — not building yet  
**Station:** **AB** (red · ABX) · Agent K  
**First real AB tool** (after thin shell: files / quiet chat / email)  
**Hands seed:** factions, members, active/inactive/unsure, leaders, names, images, field notes on involvement & behavior  

**Locked (hands):**  
- **Person-first** from day one (move factions / multi-membership without weird data)  
- **AKAs** yes  
- **Leaders:** soft-warn only — *“faction believes it has 2 leaders”* (no hard block)  
- Field notes may share **log+leaf** DNA with invent-ory (same grain, different place/tool face)  
- **Room slug:** `dossier` (not `factions`)  
- **Tool id / folder:** **`dossierDesk`** (`t/tools/dossierDesk/`, `getTool('dossierDesk', …)`) — cute and on-pattern

---

## 1. Why this tool

AB is investigation of **suppression** — success quieted, good outcomes unlogged, networks that keep things from landing.

A **dossier** is how K maps **who is in which structure**, who leads, who is still live, and what she has observed. Not a social graph toy. An **evidence shelf for people and factions**.

> [!NOTE] Hands (seed)
> First tool is a dossier of factions and members: record people, faction membership, active/inactive/unsure, mark leaders, name characters, images, field notes when researching involvements and behaviors.

---

## 2. Core objects — **person-first** (locked)

| object | grain | what it is |
|--------|--------|------------|
| **Person** | first-class crate (stem) | One human/handle/character identity — can move or sit in many factions |
| **Faction** | crate (shell) | Named structure (company, cabal, crew, Watchers, …) |
| **Membership** | link row (crate or edge+meta) | Person ↔ faction: status, role, is_leader, dates |
| **Field note** | leaf | Time-stamped observation of behavior / involvement |
| **Image** | media attach | Portrait, screencap, sigil, evidence plate |

```text
        Person ──────── Membership ──────── Faction
           │                 │                  │
           │            (status, role,          │
           │             is_leader)             │
           ├─ portrait                          ├─ sigil
           └─ field notes ◄─────────────────────┘
                    (subject = person and/or faction)
```

**Why not nested-only:** people **move** and **double-dip**. Nesting members under one faction forces duplicate people or painful migrations. Person-first is the true data.

**Membership is the join** — not “member crate owned by faction.”

### Membership fields

| field | |
|-------|--|
| `person_c_uid` | required |
| `faction_c_uid` | required |
| `status` | **active · inactive · unsure** (default **unsure**) |
| `is_leader` | bool |
| `role` | free text (“handler”, “CFO”, “cutout”) |
| optional | `joined_event`, `left_event`, notes |

### Leader rule (locked)

- Soft-warn only: **“faction believes it has 2 leaders”** (or N).  
- Do **not** hard-block a second leader flag — intelligence is often contradictory.  
- UI: banner on faction view; optional list of claimed leaders.  
- `meta.leader_c_uid` on faction may stay empty or hold “primary claim” without erasing others.

---

## 3. Person fields (v0)

| field | store | notes |
|-------|--------|--------|
| **Name / face** | `topic` / `meta.display_name` | primary display |
| **Also known as** | `meta.akas[]` | **yes — agreed** |
| **Status (global)** | optional `meta.status` | overall “still in play?” — separate from per-faction membership status |
| **Portrait** | media `role: portrait` | house media |
| **Body** | short blurb | one breath |
| **Tags** | `tags_raw` | Charlie later |

> [!NOTE] Hands
> Agreed, yes for AKAs.

> [!TIP] Agent
> Locked: `meta.akas` as list of strings; search matches primary name + AKAs.

### Faction fields (v0)

| field | store |
|-------|--------|
| **Name** | `topic` |
| **Status** | active / inactive / unsure / dissolved? |
| **Sigil / cover** | media `role: sigil` or `cover` |
| **Summary** | `body` |
| **Leader claims** | derived from memberships where `is_leader` — soft-warn if count ≥ 2 |

---

## 4. Field notes

When researching **involvements and behaviors**:

| field | |
|-------|--|
| **Subject** | person and/or faction (`subject_c_uids[]` or primary + related) |
| **When** | `event_unix` (backdate OK) |
| **Note** | body |
| **Context** | source: call, log import, sighting, rumor |
| **Confidence** | confirmed / rumor / contested |
| **Attachments** | screenshots, docs |

Notes are **leaves**, not buried JSON — same law as invent-ory and chat/glass.

> [!NOTE] Hands
> Wonder if field notes is just a different use of the log tool used for inventories?

> [!TIP] Agent
> **Same grain, different face.** invent-ory = day log + life-capture leaves on **IO**. Dossier field notes = investigation leaves on **AB**, subject-linked to person/faction.  
> Options:  
> (A) **Shared engine** (`logShell` + `logLeaf` helpers) with two tools/rooms — preferred long-term.  
> (B) Separate `dossier` kinds that **mirror** invent insert API so UX feels the same.  
> Do **not** dump AB field notes into IO invent days; place stays `terminal/ab/dossier`.  
> Shared: backdate, section-ish tags, media attach, closed-optional. Different: subject picker (person/faction), confidence, membership context.


> [!success] Hands
> Ok, agreed. just wanted to make sure.

---

## 5. Ledger sketch

| kind | scale | tool | role |
|------|-------|------|------|
| `dossier_person` | leaf (or log if we want note shelf) | `dossierDesk` | person stem |
| `dossier_faction` | log | `dossierDesk` | faction |
| `dossier_membership` | leaf | `dossierDesk` | join person↔faction |
| `dossier_note` | leaf | `dossierDesk` | field note |

**Place (AB):**
```text
sys:  terminal
dom:  ab
room: dossier      # locked
mod:  (empty)
```

**Tool naming (locked)**
| | |
|--|--|
| **Folder / `getTool` id** | **`dossierDesk`** |
| **Room slug** | `dossier` |
| **Nav / tab label** | DOSSIER (or “Dossier desk” in whisper copy) |
| **Pattern** | `fileKeeper`, `inventOry`, `chatBOX`, **`dossierDesk`** |

**Meta examples**

```json
// person
{
  "display_name": "…",
  "akas": ["…", "…"],
  "status": "unsure",
  "portrait_asset": "m.…"
}

// faction
{
  "status": "active",
  "sigil_asset": "m.…"
}

// membership
{
  "person_c_uid": "ch.…",
  "faction_c_uid": "ch.…",
  "status": "active",
  "is_leader": true,
  "role": "handler"
}

// note
{
  "subject_c_uids": ["ch.person…", "ch.faction…"],
  "subject_kind": "person|faction|both",
  "confidence": "rumor",
  "source": "field"
}
```

---

## 6. Surfaces (when we build)

### Desk v0 (person-first)
- **People** list (search name + AKAs)  
- **Factions** list (filter status)  
- **Person open** → blurb, portrait, **memberships** (add/remove faction, status, role, leader toggle)  
- **Faction open** → members via memberships; soft-warn if ≥2 leaders  
- **+ Person / + Faction / + Note**  
- Image attach (house media)

### Desk v1
- Timeline of notes across a faction  
- invent-ory / report “file into dossier note”  
- Charlie: `person*in>faction`, `leads*faction>…`  
- Suppression tags

### Explicit non-goals v0
- Full CRM / network viz  
- Auto OSINT  
- Replacing FILES  
- Hard single-leader enforcement  

---

## 7. Relation to the rest of the house

| system | relation |
|--------|----------|
| **AB FILES** | Unstructured evidence; dossier is structured claims |
| **invent-ory (IO)** | Same log+leaf DNA; different place — optional later bridge into field notes |
| **Report services** | Attention desks ≠ membership claims |
| **sendIT** | Dossier lives on **AB** |
| **Media** | Portraits / sigils / plates |
| **Charlie** | Later membership edges |

---

## 8. Tone / UX (AB)

- Red phosphor · ABX · sparse, slightly cold  
- Status: active = hard red, inactive = dim, unsure = dashed/muted red  
- Multi-leader: clear soft banner — *faction believes it has 2 leaders*  
- Empty: *no persons logged · the map is blank on purpose*  

---

## 9. Sequence

```text
[done]   AB shell + kde555 + mouse welcome
[now]    Concept locked: person-first, AKAs, soft multi-leader, notes≈log grain
[next]   dossierDesk v0: person + faction + membership + note + status + image
[later]  invent→dossier bridge, Charlie edges, richer confidence
```

---

## 10. One-line law

> **A person is the stem; a faction is a structure; membership is the claim (with doubt); a field note is what you saw — with time attached.**  
> **Leaders may disagree with reality — warn, don’t erase.**

---

## Resolved questions

| Q | A |
|---|---|
| Nested vs person-first | **Person-first** from day one |
| AKAs | **Yes** |
| Leaders | **Soft-warn** (“faction believes it has 2 leaders”) |
| Field notes vs invent-ory | **Same grain / optional shared engine; AB place + subject model** |

## Still open (light)

1. Default membership status: **unsure** (recommended) vs active?  
2. Global person status separate from membership status — keep both or only membership?

---

## Related

- [SENDIT-AND-TERMINAL-SHELVES.md](./SENDIT-AND-TERMINAL-SHELVES.md)  
- [MEDIA-AND-DIAGRAM-BOARDS.md](./MEDIA-AND-DIAGRAM-BOARDS.md)  
- [DAILY-INVENTORY-TOOL.md](./DAILY-INVENTORY-TOOL.md)  
- [REPORT-SERVICES-FROM-TERMINAL.md](./REPORT-SERVICES-FROM-TERMINAL.md)  
