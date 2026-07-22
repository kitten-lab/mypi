# Motif index — corpus defrag for logs (Charlie’s missing half)

**Status:** design / pre-runner  
**When:** after next ChatGPT export lands under `z/`  
**Goal:** discover recurring motifs, tag **message ids**, pool everything about the same thing into a **bucket**, then make meaning — without hand-reading the corpus.

This is the same impulse as **CharlieTHREADS** (tags + edges so related crates share a term and gravity), applied to the **export message graph** where the real loop-back density lives. Charlie on the ledger = live writing. Motif index = retrospective forestry over ChatGPT (and later Evernote / images / paper refs).

---

## Problem

| Fact | Consequence |
|------|-------------|
| Work is **loop-back** (touch → leave → return months later) | No single chat is “complete Boxman” |
| Material spans **logs + Evernote + paper + images** | One file is never the mind |
| Scale is beyond linear read | Manual inventory will never finish |
| Hand **seed lexicon** is incomplete | AI must **propose** motifs; humans **cull/merge** |
| Titles lie; 1000-msg chats mix ten projects | Need **message-level** multi-labels |

**Defrag ≠ finish projects.**  
**Defrag =** stable motif ids + inverted timeline of touches.

**Frag / mute / redact / commit (EDN)** is phase 2 on top of tags — not the first pass.

---

## Relationship to Charlie + ledger

| Layer | Store | Grain | Role |
|-------|--------|-------|------|
| **Charlie** | ledger `tag_map`, `thread_terms`, `thread_edges` | crate | Live posts, gravity, “what I write about now” |
| **Motif index** | `motif_index.sqlite` (or schema below) | message (+ later any source) | Historical / multi-surface pool for one idea |
| **Bridge (later)** | same slug | `motif.slug` ↔ Charlie `tag` | Open motif → crates + messages |

Charlie was always trying to capture **recurring elements in buckets**. Motifs formalize the **bucket identity** and attach **evidence** (message ids, then paper paths, Evernote notes).

Naming:

- **motif** — canonical bucket (Boxman, AIDM, third trine, …)
- **alias** — string variants that merge into one motif
- **tag (Charlie)** — live ledger label; prefer same slug as motif when bridged
- **ven / timber / branch** (nim-forester) — older extract layers; **fuel**, not authority

---

## Pipeline (high level)

```text
Pass 0  NORMALIZE   export → messages table (no AI)
Pass 1  PROPOSE     AI: motifs present in this window/chat
Pass 2  MERGE       candidates → master motifs (+ human cull)
Pass 3  TAG         AI: message_id → motif_id[] (+ conf)
Pass 4  INDEX       inverted lists, counts, first/last touch
Pass 5  FRAG        optional: line extracts for one motif (EDN later)
Pass 6  BRIDGE      optional: motif.slug → ledger tags / crates
```

Resume-safe: every AI write carries `run_id`. Re-runs don’t require full re-read if message rows unchanged.

---

## Windowing rules

ChatGPT nodes are a tree (`mapping`); we walk **time order** of real user/assistant messages (skip empty / visually hidden system nodes).

### Conversation size

| Messages (user+assistant) | Strategy |
|---------------------------|----------|
| ≤ 60 | One AI unit = whole conversation |
| 61–200 | Windows of **50** messages, **10** overlap |
| 201–800 | Windows of **40** messages, **8** overlap |
| 800+ | Windows of **40** messages, **8** overlap; **hard cap** 25 windows first pass, then continue by `window_index` |

Overlap exists so a motif that straddles a cut isn’t lost. Merge tags across windows by message_id (union of motif refs; keep max conf per motif).

### Window identity

```text
window_id = "{conversation_id}:w{window_index:04d}"
```

`window_index` starts at 0 within that conversation, ordered by message `create_time` (nulls last, then mapping order).

### What enters a window payload

For each message:

- `message_id` (mapping node id)
- `role` (`user` | `assistant` only for AI passes)
- `create_time` (if any)
- `text` — joined `content.parts` strings; truncate **per message** at 4000 chars with `…[trunc]`
- skip if text empty or whitespace only

**Window token budget (soft):** if packed text > ~24k chars, drop oldest messages from the window’s AI payload but **still leave them in DB untagged** for a later smaller window pass (record `payload_skipped=1` on those message rows for that run). Prefer shrinking window size over silent drop when building the job list.

### Priority order for first corpus pass (cull for signal)

Not all chats equal. Process in this order:

1. Filename / title hits seed **boost list** (optional; see below)
2. Message count ≥ 80
3. Recency (newer first) OR chronological — pick one per run; default **chronological** for stable timelines
4. Rest

Skip or defer titles that match pure logistics noise if needed later (`Pizza`, single-digit msg counts, etc.) — optional filter, not v1 required.

### Optional seed boost list (hints only)

Not authority. Used only to:

- prioritize which conversations to process first  
- inject into the AI system prompt as “prefer reusing these names if clearly present”

```text
Boxman, AIDM, Forgetting House, Therapy Buddy, The City, Chesters,
Genesis, Voynich, tarot, trine, Oriel, Mirror Box, Red Room,
base number / place count, rotational cube, quantum attention,
Charlie, TPS, mypi, ledger, EDN, state controller, barbie
```

Humans and Pass 2 still merge/rename freely.

---

## SQLite schema (`z/motif_index.sqlite` recommended)

Separate file from `d/_LEDGER/mypi.sqlite` so forestry experiments don’t block live tools. Bridge later by slug.

```sql
PRAGMA journal_mode = WAL;

-- Corpus provenance
CREATE TABLE IF NOT EXISTS sources (
  source_id     TEXT PRIMARY KEY,          -- e.g. export:2026-07-21
  kind          TEXT NOT NULL,             -- chatgpt_export | evernote | images | paper | other
  path          TEXT NOT NULL DEFAULT '',
  imported_at   INTEGER NOT NULL,
  meta_json     TEXT NOT NULL DEFAULT '{}'
);

CREATE TABLE IF NOT EXISTS conversations (
  conversation_id TEXT PRIMARY KEY,      -- ChatGPT conversation id
  source_id       TEXT NOT NULL,
  title           TEXT NOT NULL DEFAULT '',
  create_time     REAL,
  update_time     REAL,
  filename        TEXT NOT NULL DEFAULT '',  -- z/logs basename if split
  message_count   INTEGER NOT NULL DEFAULT 0,
  meta_json       TEXT NOT NULL DEFAULT '{}',
  FOREIGN KEY (source_id) REFERENCES sources(source_id)
);

CREATE INDEX IF NOT EXISTS idx_conv_title ON conversations(title);

CREATE TABLE IF NOT EXISTS messages (
  message_id      TEXT NOT NULL,
  conversation_id TEXT NOT NULL,
  role            TEXT NOT NULL DEFAULT '',
  create_time     REAL,
  seq             INTEGER NOT NULL,          -- 0..n-1 time order in convo
  text            TEXT NOT NULL DEFAULT '',
  text_hash       TEXT NOT NULL DEFAULT '',  -- optional sha256 prefix for change detect
  PRIMARY KEY (conversation_id, message_id),
  FOREIGN KEY (conversation_id) REFERENCES conversations(conversation_id)
);

CREATE INDEX IF NOT EXISTS idx_msg_seq ON messages(conversation_id, seq);
CREATE INDEX IF NOT EXISTS idx_msg_time ON messages(create_time);

-- Master motif list (the buckets)
CREATE TABLE IF NOT EXISTS motifs (
  motif_id      TEXT PRIMARY KEY,          -- stable: m_ + slug or ulid
  slug          TEXT NOT NULL UNIQUE,      -- charlie-friendly: boxman, aidm, third-trine
  name          TEXT NOT NULL,             -- display: "Boxman"
  type          TEXT NOT NULL DEFAULT 'other',
  -- type: project|character|world|text_work|symbol|body|product|game|practice|people|place|other
  blurb         TEXT NOT NULL DEFAULT '',  -- one line: what this bucket is
  status        TEXT NOT NULL DEFAULT 'candidate',
  -- status: candidate|active|merged|muted
  merged_into   TEXT,                      -- motif_id if status=merged
  hit_count     INTEGER NOT NULL DEFAULT 0,
  first_touch   REAL,
  last_touch    REAL,
  created_at    INTEGER NOT NULL,
  updated_at    INTEGER NOT NULL,
  meta_json     TEXT NOT NULL DEFAULT '{}'
);

CREATE INDEX IF NOT EXISTS idx_motifs_status ON motifs(status);
CREATE INDEX IF NOT EXISTS idx_motifs_hits ON motifs(hit_count DESC);
CREATE INDEX IF NOT EXISTS idx_motifs_type ON motifs(type);

CREATE TABLE IF NOT EXISTS motif_aliases (
  alias_norm    TEXT PRIMARY KEY,          -- lowercased trimmed
  motif_id      TEXT NOT NULL,
  source        TEXT NOT NULL DEFAULT 'ai', -- ai|human|seed|ven
  FOREIGN KEY (motif_id) REFERENCES motifs(motif_id)
);

-- Message ↔ motif (multi-label)
CREATE TABLE IF NOT EXISTS message_motifs (
  conversation_id TEXT NOT NULL,
  message_id      TEXT NOT NULL,
  motif_id        TEXT NOT NULL,
  conf            REAL NOT NULL DEFAULT 0.5,  -- 0..1
  source          TEXT NOT NULL DEFAULT 'ai',  -- ai|human|rule
  run_id          TEXT NOT NULL DEFAULT '',
  note            TEXT NOT NULL DEFAULT '',    -- optional short why
  created_at      INTEGER NOT NULL,
  PRIMARY KEY (conversation_id, message_id, motif_id),
  FOREIGN KEY (motif_id) REFERENCES motifs(motif_id)
);

CREATE INDEX IF NOT EXISTS idx_mm_motif ON message_motifs(motif_id);
CREATE INDEX IF NOT EXISTS idx_mm_run ON message_motifs(run_id);

-- AI / job bookkeeping
CREATE TABLE IF NOT EXISTS runs (
  run_id        TEXT PRIMARY KEY,
  phase         TEXT NOT NULL,             -- propose|tag|merge|normalize
  started_at    INTEGER NOT NULL,
  finished_at   INTEGER,
  model         TEXT NOT NULL DEFAULT '',
  notes         TEXT NOT NULL DEFAULT '',
  meta_json     TEXT NOT NULL DEFAULT '{}'
);

CREATE TABLE IF NOT EXISTS window_jobs (
  window_id       TEXT PRIMARY KEY,
  conversation_id TEXT NOT NULL,
  window_index    INTEGER NOT NULL,
  seq_start       INTEGER NOT NULL,
  seq_end         INTEGER NOT NULL,          -- inclusive
  status          TEXT NOT NULL DEFAULT 'pending',
  -- pending|proposed|tagged|error|skipped
  propose_run_id  TEXT,
  tag_run_id      TEXT,
  error           TEXT NOT NULL DEFAULT '',
  updated_at      INTEGER NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_wj_status ON window_jobs(status);
CREATE INDEX IF NOT EXISTS idx_wj_conv ON window_jobs(conversation_id);

-- Pass 1 candidates before merge (optional but useful audit)
CREATE TABLE IF NOT EXISTS motif_candidates (
  id              INTEGER PRIMARY KEY AUTOINCREMENT,
  window_id       TEXT NOT NULL,
  conversation_id TEXT NOT NULL,
  provisional_name TEXT NOT NULL,
  type            TEXT NOT NULL DEFAULT 'other',
  blurb           TEXT NOT NULL DEFAULT '',
  aliases_json    TEXT NOT NULL DEFAULT '[]',
  run_id          TEXT NOT NULL,
  resolved_motif_id TEXT,                  -- set after Pass 2
  created_at      INTEGER NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_cand_name ON motif_candidates(provisional_name);

-- External surfaces (paper, images, evernote) — stubs only until ingest
CREATE TABLE IF NOT EXISTS external_refs (
  ref_id        TEXT PRIMARY KEY,
  motif_id      TEXT,                      -- nullable until linked
  kind          TEXT NOT NULL,             -- evernote|image|paper|url|other
  path_or_id    TEXT NOT NULL DEFAULT '',
  label         TEXT NOT NULL DEFAULT '',
  note          TEXT NOT NULL DEFAULT '',
  created_at    INTEGER NOT NULL,
  FOREIGN KEY (motif_id) REFERENCES motifs(motif_id)
);

CREATE INDEX IF NOT EXISTS idx_ext_motif ON external_refs(motif_id);
```

### Hit count maintenance

After each tag run (or nightly):

```sql
UPDATE motifs SET
  hit_count = (SELECT COUNT(*) FROM message_motifs mm WHERE mm.motif_id = motifs.motif_id),
  first_touch = (SELECT MIN(m.create_time) FROM message_motifs mm
                 JOIN messages m ON m.conversation_id = mm.conversation_id
                   AND m.message_id = mm.message_id
                 WHERE mm.motif_id = motifs.motif_id),
  last_touch = (SELECT MAX(m.create_time) FROM message_motifs mm
                JOIN messages m ON m.conversation_id = mm.conversation_id
                  AND m.message_id = mm.message_id
                WHERE mm.motif_id = motifs.motif_id),
  updated_at = strftime('%s','now');
```

Only rows with `status IN ('candidate','active')` need display; `merged` should re-point queries to `merged_into`.

---

## JSONL record shapes

Append-only audit files under `z/motif_runs/{run_id}/` recommended; DB is query authority.

### `normalize_messages.jsonl`

One object per message after Pass 0:

```json
{
  "conversation_id": "uuid",
  "message_id": "uuid",
  "role": "user",
  "create_time": 1710000000.0,
  "seq": 42,
  "text": "…",
  "source_id": "export:2026-07-21",
  "title": "Chat title"
}
```

### `propose.jsonl` (Pass 1 — one line per window)

```json
{
  "run_id": "r_20260721_a1",
  "window_id": "uuid:w0003",
  "conversation_id": "uuid",
  "window_index": 3,
  "seq_start": 120,
  "seq_end": 159,
  "motifs_present": [
    {
      "provisional_name": "Boxman",
      "type": "character",
      "blurb": "Figure whose insides become outsides; projection machine.",
      "aliases": ["box man", "the projection guy"]
    },
    {
      "provisional_name": "Third trine tarot",
      "type": "project",
      "blurb": "Restructure majors; add a third trine (~1/3 block).",
      "aliases": ["third trine", "tarot restructure"]
    }
  ],
  "model": "…",
  "ts": 1720000000
}
```

### `tag.jsonl` (Pass 3 — one line per window)

```json
{
  "run_id": "r_20260721_a1",
  "window_id": "uuid:w0003",
  "conversation_id": "uuid",
  "tags": [
    {
      "message_id": "msg-uuid",
      "motifs": [
        { "name_or_slug": "boxman", "conf": 0.92 },
        { "name_or_slug": "third-trine-tarot", "conf": 0.4 }
      ],
      "note": ""
    }
  ],
  "model": "…",
  "ts": 1720000001
}
```

Resolver maps `name_or_slug` → `motif_id` via `slug` + `motif_aliases.alias_norm`. Unresolved names create **candidate** motifs (do not drop tags silently).

### `merge.jsonl` (Pass 2 — human or AI merge decisions)

```json
{
  "run_id": "r_20260721_merge1",
  "action": "merge",
  "from_motif_ids": ["m_box_man", "m_boxman"],
  "into_motif_id": "m_boxman",
  "canonical_name": "Boxman",
  "canonical_slug": "boxman",
  "ts": 1720000100
}
```

```json
{
  "run_id": "r_20260721_merge1",
  "action": "mute",
  "motif_id": "m_french-toast",
  "reason": "one-off logistics",
  "ts": 1720000101
}
```

```json
{
  "run_id": "r_20260721_merge1",
  "action": "activate",
  "motif_id": "m_boxman",
  "ts": 1720000102
}
```

On `merge`: set losers `status=merged`, `merged_into=into`; rewrite `message_motifs` and `motif_aliases` to winner; recompute hits.

---

## AI prompt contracts (short)

### Pass 1 — propose

- Input: title + numbered list `seq | message_id | role | text`
- Output: **JSON only** matching `motifs_present` array
- Rules:
  - Prefer **recurring / project / symbol / character / product** over one-off logistics
  - Multi-word names ok; avoid generic (“love”, “morning”) unless clearly a **named practice or arc**
  - Reuse boost-list names when clearly the same referent
  - 0 motifs allowed if nothing durable

### Pass 3 — tag

- Input: same window text + **frozen list** of motif names/slugs for this chat (union of Pass 1 locals + global active motifs that already have hits in this conversation or aliases present in text)
- Output: **JSON only** `tags[]`
- Rules:
  - Multi-label ok
  - Tag only if message **materially** touches motif (not mere polite echo)
  - conf: 0.3 weak, 0.6 clear, 0.9 explicit name or sustained focus
  - Prefer omit over spam

Exact system prompts live in the runner when implemented; this doc owns **IO shape**, not prose.

---

## Culling the master list (how Charlie gets a clean vocabulary)

After enough Pass 1 volume:

1. Sort motifs by `hit_count` descending  
2. **Mute** one-offs and pure logistics (`status=muted`) — keep row for audit  
3. **Merge** near-duplicates (aliases)  
4. Promote survivors to `active`  
5. Active slugs ≈ **Charlie tag vocabulary** for the archive half  

Target feel (not hard quota): dozens to low hundreds **active** motifs, not thousands of near-duplicates. Candidates can stay large; UI default shows `active` + top `candidate` by hits.

### Query that is the product

```sql
-- Bucket view: everything about Boxman in time order
SELECT m.create_time, c.title, mm.message_id, mm.conf,
       substr(m.text, 1, 200) AS excerpt
FROM message_motifs mm
JOIN messages m
  ON m.conversation_id = mm.conversation_id AND m.message_id = mm.message_id
JOIN conversations c ON c.conversation_id = mm.conversation_id
JOIN motifs mo ON mo.motif_id = mm.motif_id
WHERE mo.slug = 'boxman'
   OR mo.merged_into = (SELECT motif_id FROM motifs WHERE slug = 'boxman')
ORDER BY m.create_time;
```

(Resolve `merged_into` properly in app code: always walk to root motif.)

That **is** the Charlie bucket: pool elements → make meaning.

---

## Bridge to ledger / Charlie (later)

| Motif field | Ledger |
|-------------|--------|
| `slug` | `tag_map.tag` / Charlie term |
| active motif | optional crate `kind=motif` stub with blurb |
| message hits | stay in motif_index; link from TUI “Charlie → motif archive” |
| `thread_edges` | optional: `motif*sees>motif` when co-tagged in same window often |

Do **not** require ledger writes for Pass 0–4. Charlie live path stays fast; archive is bulk.

---

## Alignment with existing forestry

| Existing | Use |
|----------|-----|
| `z/json-sawmill.py` | Pass 0 split → `z/logs` |
| `z/import-forester-logs.php` | branches / timbers / vens — optional import of vens as alias **hints** |
| `t/tools/foresterSEARCH` | keyword UI until motif TUI exists |
| `ledger/*` | live Charlie; bridge by slug later |

Motif index **does not replace** forester overnight; it becomes the **authority for “what is this about”** once tagging coverage is real.

---

## Implementation sketch (when export arrives)

1. Drop new export at `z/conversations.json` (or dated name); run sawmill → `z/logs`.  
2. Script `z/motif_normalize.py` — fill `sources`, `conversations`, `messages`, create `window_jobs`.  
3. Script `z/motif_propose_tag.py` — batch windows through model API; write JSONL + DB.  
4. Script `z/motif_merge_cli.py` — list top candidates; merge/mute/activate.  
5. Report: `motif_report.py` top motifs + sample excerpts.  
6. Optional: TUI section or small web face “open slug → timeline”.

**Out of scope for first runner:** Evernote full ingest, OCR paper, EDN frag verbs, full ledger dual-write.

---

## Success criteria (v1)

- [ ] All (or vast majority) of export messages in `messages`  
- [ ] Master list with merge/mute workflow  
- [ ] Message multi-tags for processed windows  
- [ ] Query: one slug → chronological evidence list  
- [ ] You can open “Boxman” or “AIDM” and **see the pool**, not a single chat title  

Meaning-making stays human (and later assisted). **Pooling is machine.**

---

## Open choices (decide at implement time)

| Choice | Default |
|--------|---------|
| DB path | `z/motif_index.sqlite` |
| Model / API | whatever is available in-env for batch (local or remote); record in `runs.model` |
| First pass coverage | all convos ≥ N msgs, or boost-list first |
| Slug algorithm | lower, `[a-z0-9]+` hyphenated, max 48 chars |
| conf threshold for hit_count | count all tags; UI filters conf ≥ 0.5 |

---

## One-line thesis

**Charlie wants buckets. Motifs are named buckets. Message tags are the evidence. Cull until the active list is a usable language for your corpus — then meaning has a place to land.**
