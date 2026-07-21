# Cleaning the half-migration

## Decision

| Layer | Policy |
|-------|--------|
| **Cosmology** | **SYS · DOM · ROOM(ROM) · MOD** (modifier owns room) |
| **Craft keep** | `define()` in SKY_SIG, SKY_AUTH chain, getTool, letter junctions |
| **Machine env** | `COMMANDCENTER9` (this box); `ROSEWOOD8` legacy local alias only |
| **Local pretty URL** | Vhost DocumentRoot = SYS → path is **`/DOM/KEY`** only |
| **Public** | May nest SYS in path (`/book/DOM/KEY` on b.imported.to) — later |
| **Online** | Nice if it works; **tool is for you first** |

## What “clean” means this pass

1. One local-env check everywhere (COMMANDCENTER9 | ROSEWOOD8 | LOCAL).  
2. Nav/hrefs on local vhosts never prefix extra SYS (book fixed; same rule for others).  
3. Document tools kept + shared ledger.  
4. No full rewrite of every surface before News + postBASIC→ledger.

## Contact rule

When a Surface breaks: align **that** nav/SIG/route to `/DOM/KEY` + defines.  
Do not start a citywide rename festival.
