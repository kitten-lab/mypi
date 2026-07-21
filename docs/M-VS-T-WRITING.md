# m/ doors vs t/ tools — who writes how

| Layer | Path | Writing style |
|-------|------|----------------|
| **Doors / rooms** | `m/doors/{sys}/{dom}/{key}.php` | **Sky only** — `SKY__AUTH`, `openSky`, `getTool`, `leaf`/`medHeading`/`hr`/`closeSky`. **No raw page HTML.** |
| **Tools** | `t/tools/{pack}/page*.php` | **Echo / HTML OK** — rendered inside MAIN when `setGET("set")` runs the tool. wireWORDS / forms live here. |

**Wire unfinished:** a fuller sky DSL for *everything* was never finished; tools stay HTML/echo until then.

**Rule for agents:** fix content in **t/** with normal templates; only **m/** must stay sky composition.
