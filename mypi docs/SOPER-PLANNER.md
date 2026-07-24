# SOPER — product planner (signed extract pack)

**Status:** draft from glass hunt pass 2 — **not signed until you approve sections**  
**Date:** 2026-07-23  
**Method:** content windows from NT glass (titles ignored). Full dumps under `mypi docs/extracts/`.

## Sources (face / cid)

| face | cid | date | role in this planner |
|------|-----|------|----------------------|
| `698` | `69e91747-78b4-83ea-afb1-ffd046e7dc7b` | 2026-04-22 | PostBasic vs fragment primitive; composition layer |
| `702` | `69ea45c6-ffe8-83ea-ae26-5b90e72c008b` | 2026-04-23 | Section vs topic; deep SOPER density |
| `708` | `69edee2e-d20c-83ea-b7bf-41a380d0c03e` | 2026-04-26 | SOPR = structure *already-done* thinking |
| `740` | `69fe5e77-be7c-83ea-ba75-d6a78f94afcc` | 2026-05-08 | Stratified stack; SOPERView; Silo/Skyline |
| `870` | `6a3a46dc-410c-83ea-b70d-201ffe913ac6` | 2026-06-23 | One store, many writers; getTool shape |
| `696` | `69e752a8-4054-83ea-ac42-d73ac003c5f5` | 2026-04-21 | Fragments first; Mother of Vens origin note |

Live code already partially implements this as **`soprBASIC`** · kind `soper` · `topic` = section · `meta.section_slug` (see `TOOL-LEDGER-STORE.md`).

---

## 1. What SOPER *is* (claims)

### 1.1 Concept vs tool

| Name | Meaning | Source |
|------|---------|--------|
| **SOPER** (concept) | Field of fragments — Heraclitus-level collections; rearrangeable, revisitable, recomposable | face 698 |
| **soprBASIC / SOPER Basic** (tool) | Places a fragment into that field | face 698, 870 |
| **Demo page / surface** | A *site that uses* the tool — not the product identity | face 740 |

> “SOPER is a tool. This is a site that uses the tool… Anything can make a SOPER page.” — face 740

### 1.2 Not the whole product

| Claim | Source |
|-------|--------|
| SOPER = **fragment ingestion**, not “the system” and not “the product” | face 708 |
| SOPR is for **structuring thinking that already happened**, not inventing thinking | face 708 |
| Needs **authentic high-density thought material** (e.g. AIDM fragments), not “whatever is available” | face 708 |

### 1.3 Stratified layers (do not collapse)

From face 740 (user-corrected stack):

| Layer | Job |
|-------|-----|
| **postBASIC** | Lightweight authoring — posts / freeform lines |
| **SOPER ingest** | Parser/interpreter → section numbering, IDs, hierarchy, org semantics |
| **SOPERView / SoberView** | Render posts *as* fragments (same styling) **without** sections / auto-numbering |

Auto-numbering (e.g. `intention 0001`) only happens when content is **ingested as SOPER sections**, not when merely viewed through SOPERView.

### 1.4 Post vs fragment (species split)

| | PostBasic | Fragment / soprBASIC |
|--|-----------|----------------------|
| Says | “this happened / this is a thing” | “this belongs *here*” |
| Identity | expressive, temporal, broadcast | atomic, positional, composable |
| Heading | topic / subject line | **section** (group + sort) |
| Order | often time / feed | **not baked into the unit** — applied later |

> “SOPER isn’t a post surface—it’s a fragment field.” — face 698  
> “Soaper’s basically post-basic modified so that instead of title/topic it has the concept of a **section**.” — face 702

### 1.5 One store, many writers (ontology)

From face 870 (user): everything stores in the **same-shaped data system**. Distinctions are **authoring experiences**, not separate databases:

| Writer | Opinion at create time |
|--------|------------------------|
| Guestbook | minimal metadata |
| soprBASIC | **section-oriented** fragments |
| postBASIC | topic-oriented fragments |
| reportBASIC | topic + reporter/origin |
| Mailroom | communication-oriented |
| Jukebox | music-oriented |

Danger called out: premature multi-object types that later need merging. Prefer feed fragments until a shape *fails*.

### 1.6 Capture vs composition

PostBasic / ingest = **creation**. Missing layer named in glass:

> a second layer that can say “these fragments, in this order, form a document” — Assembler / Weaver / Compiler — face 698

Options discussed (not all implemented):

- **A** Keep PostBasic pure; order derived / soft  
- **B** Separate curation/assembly tool (Confluence-like)  
- **C** Order = time of entry  

**Product rule preferred in glass:** do **not** force order into the fragment payload; composition is a later / separate act. Duplicates in same symbolic position are **signal**, not always bugs (face 698).

### 1.7 Product nesting (pocket internet)

| Name | Role | Source |
|------|------|--------|
| **Silo / pocket internet** | Overarching product/ecosystem | face 740 |
| **Skyline** | One institutional surface (bureaucratic reporting) | face 740 |
| **SOPER** | Substrate: ingestion / composition / doc tooling inside that world | face 740 |

Framing for demos: “my version of **Git documentation** for Git” — machinery that produces structured artifacts repeatedly, not one handcrafted page (face 740).

### 1.8 Mother of Vens (adjacent origin)

Face 696: early “simple notes app” need tied to collecting material for **Mother of Vens** / ADM notes — lineage of fragment tooling, not a SOPER feature list. Keep as **lore/origin**, not a SOPER module name unless you re-sign it.

---

## 2. What already matches live mypi

| Glass claim | Live today |
|-------------|------------|
| kind = fragment | `kind` = `soper` |
| section not title | `topic` = section; `meta.section_slug` |
| ledger-shaped crate | `mypi_ledger_create_post` rail (`TOOL-LEDGER-STORE.md`) |
| getTool split writers | `getTool("soprBASIC", "AddFragment")` / `ViewList` (face 870 screenshot pattern) |

---

## 3. Open decisions (need your sign-off)

Mark each: **yes / no / later**.

1. **Order storage:** never on fragment vs assembly tool vs soft UI reorder only?  
2. **SOPERView** as first-class desk (post list in fragment chrome) vs only ingest path?  
3. **Rename:** keep `soprBASIC` / `soper` or rebrand to FragmentBasic publicly?  
4. **Assembler** as separate tool name (Weaver / Compile / book export) — when?  
5. **Duplicates after delete:** max-scan renumber vs allow gaps vs allow intentional dup symbolic slots?  
6. **Demo surface:** keep Silo/Skyline naming in public copy or pocket-only lore?

---

## 4. Suggested v1 product surface (proposed — unsigned)

Minimal desk that *is* SOPER without swallowing the whole house:

1. **AddFragment** — section + body (+ optional tags/Charlie)  
2. **ViewList** — group by section_slug, show auto IDs if present  
3. **SOPERView mode** — optional: show postBASIC rows in fragment chrome (no sections)  
4. **No mega-tool** — List / View / Tag as separate Fns (aligns with CORE grief notes)

Out of scope for v1: full Assembler, spatial rearrange canvas, Git export.

---

## 5. Extract paths (for re-read)

- Index: `mypi docs/extracts/INDEX-PASS2.md`  
- Faces: `face-698-soper.md`, `face-702-soper.md`, `face-708-soper.md`, `face-740-soper.md`, `face-870-soper.md`, `face-696-soper.md`  
- Hunt pass 1: `mypi docs/HUNT-AIDM-PRODUCT-PASS1.md`

---

## 6. Sign block

| Section | Approved? | Notes |
|---------|-----------|-------|
| 1.1 Concept vs tool | | |
| 1.2 Not whole product | | |
| 1.3 Stratified layers | | |
| 1.4 Post vs fragment | | |
| 1.5 One store many writers | | |
| 1.6 Capture vs composition | | |
| 1.7 Product nesting | | |
| 4 Suggested v1 surface | | |

When signed, promote claims into desk work + codex; leave unsigned sections as lore only.
