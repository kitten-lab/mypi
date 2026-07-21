# postBASIC → base tool + real ledger

**Context:** Nothing of durable personal value is trusted in the wild JSON slips yet.  
**Goal:** Think/make on a Surface → becomes the database.  
**Doctrine:** Half the tagging is **where you posted** (surface/dom/room). Edits leave history.

---

## What postBASIC already gets right (keep as *shape*)

From living chests (e.g. `d/starline/offices-frontdesk.post.json`) and EDN `_store.php`:

| Field family | Purpose |
|--------------|---------|
| **cUID** | Stable crate id (`crate.…`) |
| **payload.post** | topic / leaf (body), agent |
| **tags** + raw + parser meta | Charlie-style structured tags |
| **import_env** | **Where posted:** sys/dom/room (+ display names), mod |
| **tps** | tUID, ingest_unix, event_unix, timezone |
| **assistant** | tool name/version (postBASIC) |
| **ref_material** | optional source links |

**Keep this information model.**  
**Drop as primary store:** one `.post.json` file per room slug, duplicated under `d/`, `--archive`, DEMO, EDN `Storage/Tools/postBASIC/`, twin `.posts.json`, merge hell.

---

## What’s wrong today

```text
getTool(postBASIC, MakePost)
  → chestersCRATES / file_put
  → d/{surface}/…-{room}.post.json   (and siblings, archives, EDN paths…)
```

- Query = crawl folders.  
- Edit = overwrite or duplicate.  
- Migrate = archaeology.  
- Trust = zero for real thoughts.

---

## Target: one ledger, postBASIC is the pen

```text
Surface (where I am)
  getTool("postBASIC", "MakePost" | "SoperView" | …)
        │
        ▼
  ledger API  (single root)
        │
        ├── crates          current snapshot (queryable)
        ├── crate_events    append-only alterations
        └── indexes         by tag, by place, by time
```

### Schema (v0 — SQLite recommended)

**File:** e.g. `d/_LEDGER/mypi.sqlite` (or `Storage/ledger.sqlite` under EDN-aligned root)  
One file, one migration story, portable to next build.

#### `crates`

| Column | Notes |
|--------|--------|
| `c_uid` | PK, text |
| `kind` | `post` (later: invent-ory, fragment, …) |
| `topic` | |
| `body` | leaf/content |
| `agent` | user / assistant / mod |
| `tool` | `postBASIC` |
| `tool_version` | int |
| `place_sys` | e.g. starline — **auto from Surface** |
| `place_dom` | e.g. offices |
| `place_room` | e.g. frontdesk |
| `place_path` | denormalized `starline/offices/frontdesk` for query |
| `tags_json` | structured tags current |
| `tags_raw` | |
| `event_unix` | story/event time |
| `ingest_unix` | membrane hit |
| `timezone` | |
| `t_uid` | TPS id string |
| `meta_json` | viewport, ref_material, extras |
| `created_at` / `updated_at` | |

#### `crate_events` (append-only)

| Column | Notes |
|--------|--------|
| `id` | integer PK |
| `c_uid` | FK |
| `event_type` | `create` \| `set_body` \| `set_topic` \| `tag_add` \| `tag_remove` \| `set_place` \| … |
| `payload_json` | diff or full field patch |
| `actor` | who |
| `surface_path` | where the edit was made |
| `event_unix` / `ingest_unix` | TPS |
| `tool` | postBASIC / sky editor / … |

**Rule:** every write updates snapshot **and** inserts event(s). Never silent overwrite of history.

#### Optional indexes (tables or SQL indexes)

- `idx_crates_place` on `place_path`  
- `idx_crates_ingest` on `ingest_unix`  
- `tag_map(c_uid, tag_key, tag_val)` for fast “by tag”  
- Place tags **always** written on create from `import_env` (e.g. `surfaces*star>starline` + structured place fields)

---

## Place-as-tag (your doctrine)

On create (and on move):

1. Store **place_sys / dom / room** as first-class columns.  
2. Also emit structured tags from place (so Charlie/Dewey habits still work).  
3. User tags are **extra**; place tags are **automatic half**.

“Where I posted” is never only buried in a filename again.

---

## postBASIC tool rework (behavior)

| Function | Job |
|----------|-----|
| **MakePost** | Form → ledger create + events; show cUID + place |
| **SoperView / ViewList** | Query ledger (filter by place default = here; optional all) |
| **Edit** (new) | Change body/tags → events + snapshot |
| **History** (new) | List events for cUID |

Keep **getTool("postBASIC", "MakePost")** as the call shape.  
Swap the backend from `file_put_contents(room.post.json)` to **ledger API**.

EDN ToolBringer path and mypi `t/tools/postBASIC` should call the **same** ledger module (one PHP include under `k/systems/ledger/` or similar).

---

## Migration stance (you have nothing precious)

1. **Do not** invest in merging all archive DEMO slips into production.  
2. Optional: one-shot importer for *demo* chests into ledger for testing.  
3. Wild `.post.json` become **export format** or read-only archive, not write target.  
4. Export button later: dump ledger → JSON for next build (stable schema version field).

**Schema version:** `ledger_meta(key, value)` with `schema_version = 1`.

---

## Trust checklist (when you’ll put real thoughts in)

- [ ] Create post from starline front desk → row in sqlite with place filled  
- [ ] Same post visible from ViewList filtered by place **and** global list  
- [ ] Add a tag → event row + snapshot tags update  
- [ ] Change body → event + new body; old body recoverable from events  
- [ ] Copy `mypi.sqlite` alone = full backup of thoughts  
- [ ] Document field list matches or exceeds old chest (cUID, topic, leaf, tags, place, tps)

---

## Implementation order

1. `k/systems/ledger/` — open db, migrate schema, `crate_create`, `crate_patch`, `crate_get`, `crate_list`, `crate_history`  
2. Wire **MakePost** actor to ledger (one Surface first)  
3. Wire **SoperView** to `crate_list` (default place = current room)  
4. Minimal **History** view (even plain HTML)  
5. Deprecate write path to `.post.json` (read-only fallback optional)  
6. Then getRom / workshop / sky editor on same ledger  

---

## Out of scope for this rework

- Perfect CSS  
- Full Dewey rebuild  
- miwbs integration (only “same ids later”)  
- Bulk Glass log import  

---

## One sentence

**postBASIC stays the base pen and getTool entry; the paper becomes one versioned SQLite ledger with place-as-half-the-tags and append-only alterations — so thinking is the database and the next build can migrate one file.**
