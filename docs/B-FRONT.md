# Unified port `b` (no more hosts per surface)

## Policy

| Do | Don’t |
|----|--------|
| Reach every SYS as `http://b/{sys}/…` | Add `hosts` + vhost for every new surface |
| Href helper: `/{sys}/{dom}/{key}` | Fake domains only for nostalgia |
| Pocket browser uses `b` only | Force `starline` / `www` / `book` ServerNames for daily use |

Dedicated vhosts (if still in Apache) remain optional; **keyMaker** accepts both:

- `/news/headlines` on DocumentRoot `b/starline`
- `/starline/news/headlines` on DocumentRoot `b`

## Rewrite

`b/.htaccess` sends `/{sys}/…` → `{sys}/index.php` while keeping `REQUEST_URI` for routing.

## Address bar / leadline

| Surface | What you see |
|---------|----------------|
| **Pocket browser** | No browser URL chrome. Window title is path-only (`mypi · starline/news/headlines`) — host `b` never appears. |
| **WWW shell bar** | Path with optional **hide `/www` prefix** so it feels like site-root while the real URL stays under `b`. |
| Chrome/Edge (debug) | Full `http://b/{sys}/…` — fine; not the daily door. |

**Policy:** new SYS = new folder under `b/` + open `/that-sys/…`. Never a new hosts entry for a surface you only use inside the pocket.

## Examples

```text
http://b/starline/news/headlines
http://b/starline/chester/crates
http://b/www/public/home
http://b/book/terminal_girls/oriel
http://b/          → list SYS folders
```
