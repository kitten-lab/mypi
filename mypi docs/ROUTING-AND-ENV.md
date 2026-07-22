# Routing, env, SYS/DOM/MOD

## Machine names are not cosmologies

| Tag | Meaning |
|-----|---------|
| **ROSEWOOD8** | Old laptop (legacy; still accepted as local) |
| **COMMANDCENTER9** | This machine (current `$ENV`) |
| **LOCAL** | Optional generic local alias |

These only switch **letter hosts vs public imported.to roots** and whether pretty URLs omit a nested SYS slug. They are **not** SYS/DOM/MOD.

## Cosmology (keep / restore)

```text
SYS   — system / surface form (book, starline, Terminal, …)
DOM   — domain of service under SYS (fragments, terminal_girls, IO, …)
ROOM  — page-like space (key → file under m/doors/… for now)
ROM   — room-in-room (later)
MOD   — modifier: owns/modifies the room
KEY   — unlock into the room (pretty: /DOM/KEY)
```

## What half-migration tried (keep the good parts)

**Keep:**

- `define('WORLD_ID'|WORLD_TAG|BLOCK_ID|BLOCK_URI|…)` in `-SKY_SIG-*.php`
- SKY_AUTH → SIG → complexRoutes → invokeSky → env → local SIG pattern
- Letter junctions a/b/c/d/k/m/t
- getTool composition

**Restore / clean toward:**

- Explicit **SYS / DOM / ROOM / MOD** in myth and (gradually) in data
- Local pretty URLs: **`/DOM/KEY`** when vhost DocumentRoot is already the SYS  
  (not `/SYS/DOM/KEY` — that double-book bug)
- Stop inventing parallel soft names mid-file

**Do not:**

- Full rewrite of every archive surface before News + ledger
- Throw away SKY_AUTH structure to “start clean”

## Local vs public URL

| Mode | Example |
|------|---------|
| Local vhost `ServerName book` | `http://book/fragments/intentions` |
| Public nested | `https://b.imported.to/book/fragments/intentions` (SYS in path) |

keyMaker uses empty local slug on COMMANDCENTER9/ROSEWOOD8/LOCAL.
