# WWW — master browser chrome (not DEMO tenant shell)

## What was wrong

`DEMO/WWW` is a **tenant** (doors + a thin white DEMO shell). Live rooms
(`danyi`, `EXE-708`, …) quickDress **`wwwExplorer_innerShell`** — that class
lives on the **master WWW / Interra explorer chrome**, not on DEMO/WWW’s
white `MAIN` layout.

Putting DEMO’s shell on SYS `www` made rooms “work” but look like a file dump
inside the wrong frame.

## Canonical chrome (source of truth)

| Live | Twin / origin |
|------|----------------|
| `a/www/asSys/shell.php` | same structure as `a/interra/asSys/shell.php` |
| `a/www/asSys/style.css` | copy of interra explorer CSS, title `<|> WWW -` |
| `a/www/asSys/fonts.css` | VT323 etc. from interra |
| `a/--archive/SDK-808/WWW` | early static Concept of Connection mock |
| `a/--archive/DEMO/WWW` | **tenant** shell only — do not use as master |

Kitten: `k/kittens/webBAR.kitten.js` (back / forward / address / GO).

## Rooms (m/)

Still your archive doors: `m/doors/www/{danyi,find,public,EXE-708,games,roms,private}/`

## URL

```text
http://b/www/danyi/index
```

Styles: `http://a/www/asSys/style.css` (letter host `a` is correct).

## Not this chrome

Remake Win-box: `m/doors/www/--remake-not-archive/` (parked, unused).
