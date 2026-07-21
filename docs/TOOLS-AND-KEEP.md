# Tools to keep + shared ledger

You use (and want to keep) at least:

| Tool | Typical payload | Status |
|------|-----------------|--------|
| **postBASIC** | topic + leaf (post / headline) | base pen → ledger first |
| **soprBASIC** | fragments / list views | same tool shape, different `kind` |
| **chatBOX** | chat messages / rooms | same ledger, `kind=chat` (later) |
| **cuBOOK** | guest/book posts | same shape if/when wired |
| forester*, jsonVIEW, skyGEN… | keep in tree; not v1 trust path | |

## Pattern (do not throw away)

```text
getTool("postBASIC", "MakePost");
getTool("soprBASIC", "ViewList");
getTool("chatBOX", "ChatBox");
```

act / set / kit (or page/actor in mypi -v1/-v2) compose onto the Surface.

## Store rule

**One ledger** (`d/_LEDGER/mypi.sqlite`):

- `kind` distinguishes payload family: `post`, `fragment`, `chat`, …  
- SYS/DOM/ROOM/MOD (or place_path until columns renamed) = **where posted**  
- `crate_events` = alterations for **all** kinds  

Wild per-room `.post.json` / tool-specific slips → **deprecate writes**; optional read-only import later.

## EDN Terminals (later problem — park)

IO / JX / CU / DM under `go.edn` are **not** mypi Surfaces. Their data lives under EDN `Storage/Terminal/…` and browser JS stores.

| Now | Later |
|-----|--------|
| Use them as **doors** from pocket browser | Adapter: import fragments/logs → ledger crates |
| Do not block mypi cleanup | No “merge EDN into mypi folders” until ledger is trusted |

EDN is a **parts + Terminal ROM** bin, not a reason to delay SYS/DOM cleanup on mypi.
