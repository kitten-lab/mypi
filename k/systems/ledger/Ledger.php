<?php
/**
 * mypi ledger — single SQLite store (shared with Python mypi_tui).
 * Cosmology fields: sys, dom, room, mod (not world/block in new writes).
 * DB: d/_LEDGER/mypi.sqlite
 */

if (!function_exists('mypi_ledger_path')) {
    function mypi_ledger_path() {
        if (!defined('echoSONAR')) {
            throw new RuntimeException('echoSONAR not defined');
        }
        $dir = rtrim(str_replace('\\', '/', echoSONAR), '/') . '/d/_LEDGER';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        return $dir . '/mypi.sqlite';
    }

    function mypi_ledger_pdo() {
        static $pdo = null;
        if ($pdo instanceof PDO) {
            return $pdo;
        }
        $path = mypi_ledger_path();
        $pdo = new PDO('sqlite:' . $path, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA journal_mode = WAL');
        mypi_ledger_migrate($pdo);
        return $pdo;
    }

    function mypi_ledger_migrate(PDO $pdo) {
        $pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS ledger_meta (
  key TEXT PRIMARY KEY,
  value TEXT NOT NULL
);
CREATE TABLE IF NOT EXISTS crates (
  c_uid TEXT PRIMARY KEY,
  kind TEXT NOT NULL DEFAULT 'post',
  topic TEXT NOT NULL DEFAULT '',
  body TEXT NOT NULL DEFAULT '',
  agent TEXT NOT NULL DEFAULT 'user',
  tool TEXT NOT NULL DEFAULT 'postBASIC',
  tool_version INTEGER NOT NULL DEFAULT 1,
  place_path TEXT NOT NULL DEFAULT '',
  place_label TEXT NOT NULL DEFAULT '',
  sys TEXT NOT NULL DEFAULT '',
  dom TEXT NOT NULL DEFAULT '',
  room TEXT NOT NULL DEFAULT '',
  mod TEXT NOT NULL DEFAULT '',
  tags_json TEXT NOT NULL DEFAULT '[]',
  tags_raw TEXT NOT NULL DEFAULT '',
  event_unix INTEGER,
  ingest_unix INTEGER NOT NULL,
  timezone TEXT NOT NULL DEFAULT '',
  t_uid TEXT NOT NULL DEFAULT '',
  meta_json TEXT NOT NULL DEFAULT '{}',
  created_at INTEGER NOT NULL,
  updated_at INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_crates_ingest ON crates(ingest_unix DESC);
CREATE INDEX IF NOT EXISTS idx_crates_place ON crates(place_path);
CREATE INDEX IF NOT EXISTS idx_crates_sys ON crates(sys);
CREATE INDEX IF NOT EXISTS idx_crates_kind ON crates(kind);
CREATE TABLE IF NOT EXISTS crate_events (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  c_uid TEXT NOT NULL,
  event_type TEXT NOT NULL,
  payload_json TEXT NOT NULL DEFAULT '{}',
  actor TEXT NOT NULL DEFAULT '',
  place_path TEXT NOT NULL DEFAULT '',
  event_unix INTEGER,
  ingest_unix INTEGER NOT NULL,
  tool TEXT NOT NULL DEFAULT ''
);
CREATE INDEX IF NOT EXISTS idx_events_cuid ON crate_events(c_uid, id);
CREATE TABLE IF NOT EXISTS tag_map (
  c_uid TEXT NOT NULL,
  tag TEXT NOT NULL,
  PRIMARY KEY (c_uid, tag)
);
CREATE INDEX IF NOT EXISTS idx_tag_map_tag ON tag_map(tag);
CREATE TABLE IF NOT EXISTS deleted_log (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  c_uid TEXT NOT NULL,
  snapshot_json TEXT NOT NULL,
  deleted_at INTEGER NOT NULL,
  actor TEXT NOT NULL DEFAULT '',
  hard INTEGER NOT NULL DEFAULT 0
);
CREATE TABLE IF NOT EXISTS tps_shelves (
  tps_uid TEXT PRIMARY KEY,
  window_unix INTEGER NOT NULL,
  window_seconds INTEGER NOT NULL,
  clock_id TEXT NOT NULL DEFAULT 'gaia',
  facets_json TEXT NOT NULL DEFAULT '{}',
  created_at INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_tps_window ON tps_shelves(window_unix DESC);
CREATE TABLE IF NOT EXISTS tps_attach (
  tps_uid TEXT NOT NULL,
  c_uid TEXT NOT NULL,
  kind TEXT NOT NULL DEFAULT 'post',
  seq INTEGER NOT NULL DEFAULT 0,
  attached_at INTEGER NOT NULL,
  PRIMARY KEY (tps_uid, c_uid)
);
CREATE INDEX IF NOT EXISTS idx_tps_attach_crate ON tps_attach(c_uid);
CREATE TABLE IF NOT EXISTS thread_edges (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  c_uid TEXT NOT NULL,
  from_term TEXT NOT NULL,
  rel TEXT NOT NULL,
  to_term TEXT NOT NULL,
  ingest_unix INTEGER NOT NULL,
  sys TEXT NOT NULL DEFAULT '',
  dom TEXT NOT NULL DEFAULT '',
  room TEXT NOT NULL DEFAULT '',
  mod TEXT NOT NULL DEFAULT ''
);
CREATE INDEX IF NOT EXISTS idx_edges_from ON thread_edges(from_term);
CREATE INDEX IF NOT EXISTS idx_edges_to ON thread_edges(to_term);
CREATE INDEX IF NOT EXISTS idx_edges_crate ON thread_edges(c_uid);
CREATE TABLE IF NOT EXISTS thread_terms (
  term TEXT PRIMARY KEY,
  gravity INTEGER NOT NULL DEFAULT 0,
  updated_at INTEGER NOT NULL
);
SQL
        );
        // Add columns if DB was created by older Python schema
        $cols = [];
        foreach ($pdo->query('PRAGMA table_info(crates)') as $row) {
            $cols[$row['name']] = true;
        }
        foreach (['sys', 'dom', 'room', 'mod'] as $c) {
            if (empty($cols[$c])) {
                $pdo->exec("ALTER TABLE crates ADD COLUMN $c TEXT NOT NULL DEFAULT ''");
            }
        }
        if (empty($cols['deleted_at'])) {
            $pdo->exec('ALTER TABLE crates ADD COLUMN deleted_at INTEGER');
        }
        $pdo->prepare(
            'INSERT INTO ledger_meta(key, value) VALUES(?, ?)
             ON CONFLICT(key) DO UPDATE SET value=excluded.value'
        )->execute(['schema_version', '2']);
        $pdo->prepare(
            'INSERT INTO ledger_meta(key, value) VALUES(?, ?)
             ON CONFLICT(key) DO NOTHING'
        )->execute(['tps_window_seconds', '900']);
    }

    function mypi_ledger_new_cuid() {
        return 'crate.' . strtoupper(bin2hex(random_bytes(8)));
    }

    function mypi_ledger_tps_window_seconds(PDO $pdo = null) {
        $pdo = $pdo ?: mypi_ledger_pdo();
        $st = $pdo->prepare("SELECT value FROM ledger_meta WHERE key='tps_window_seconds'");
        $st->execute();
        $row = $st->fetch();
        $w = $row ? (int) $row['value'] : 900;
        return $w > 0 ? $w : 900;
    }

    function mypi_ledger_window_unix($event_unix, $window_seconds = null) {
        $w = $window_seconds ?? mypi_ledger_tps_window_seconds();
        $e = (int) $event_unix;
        return $w <= 1 ? $e : ($e - ($e % $w));
    }

    function mypi_ledger_tps_uid($window_unix, $window_seconds) {
        return $window_unix . '.w' . $window_seconds;
    }

    function mypi_ledger_tps_facets($window_unix) {
        $u = (int) $window_unix;
        $dt = new DateTime('@' . $u);
        $dt->setTimezone(new DateTimeZone('UTC'));
        $y = (int) $dt->format('Y');
        $dayStart = gmmktime(0, 0, 0, (int) $dt->format('n'), (int) $dt->format('j'), $y);
        $pulse = (int) floor((($u - $dayStart) * 1000) / 86400);
        $pulse = max(0, min(999, $pulse));
        return [
            'utc' => $dt->format('c'),
            'year' => $y,
            'millennium' => intdiv($y, 1000),
            'century_residual' => intdiv($y % 1000, 100),
            'decade_residual' => intdiv($y % 100, 10),
            'block' => intdiv($u, 10000),
            'unix_mod_9' => $u % 9,
            'machine_pulse' => $pulse,
            'clock_id' => 'gaia',
        ];
    }

    /**
     * @return array{tags:list<string>,edges:list<array{from:string,rel:string,to:string}>}
     */
    function mypi_ledger_parse_charlie($tags_raw) {
        $tags = [];
        $edges = [];
        $raw = trim((string) $tags_raw);
        if ($raw === '') {
            return ['tags' => [], 'edges' => []];
        }
        $parts = preg_split('/[;\n]+/', $raw);
        $chunks = [];
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p === '') {
                continue;
            }
            if (strpos($p, '*') !== false && strpos($p, '>') !== false) {
                $chunks[] = $p;
            } else {
                foreach (preg_split('/[\s,]+/', $p) as $c) {
                    if (trim($c) !== '') {
                        $chunks[] = trim($c);
                    }
                }
            }
        }
        foreach ($chunks as $chunk) {
            $chunk = ltrim(trim($chunk), '#');
            if ($chunk === '') {
                continue;
            }
            if (preg_match('/^(.+?)\*(.+?)>(.+)$/u', $chunk, $m)) {
                $from = strtolower(trim($m[1]));
                $rel = strtolower(trim($m[2]));
                $to = strtolower(trim($m[3]));
                if ($from !== '' && $to !== '') {
                    $edges[] = ['from' => $from, 'rel' => $rel, 'to' => $to];
                    // a, connector (rel), c, and full chain — connector is a real term too
                    foreach ([$from, $rel, $to, $from . '*' . $rel . '>' . $to] as $t) {
                        if ($t !== '' && !in_array($t, $tags, true)) {
                            $tags[] = $t;
                        }
                    }
                }
            } else {
                $t = strtolower($chunk);
                if (!in_array($t, $tags, true)) {
                    $tags[] = $t;
                }
            }
        }
        return ['tags' => $tags, 'edges' => $edges];
    }

    function mypi_ledger_parse_tags($tags_raw, $sys, $dom, $room, $mod) {
        $parsed = mypi_ledger_parse_charlie($tags_raw);
        $tags = $parsed['tags'];
        $path = trim(implode('/', array_filter([$sys, $dom, $room], 'strlen')), '/');
        if ($path !== '') {
            $tags[] = 'path:' . $path;
            foreach (explode('/', $path) as $seg) {
                $seg = strtolower($seg);
                if ($seg !== '' && !in_array('@' . $seg, $tags, true)) {
                    $tags[] = '@' . $seg;
                }
            }
        }
        if ($mod !== '') {
            $tags[] = 'mod:' . strtolower($mod);
        }
        if ($sys !== '') {
            $tags[] = 'sys:' . strtolower($sys);
        }
        if ($dom !== '') {
            $tags[] = 'dom:' . strtolower($dom);
        }
        return $tags;
    }

    function mypi_ledger_tps_ensure_and_attach(PDO $pdo, $c_uid, $kind, $event_unix, $ingest_unix) {
        $w = mypi_ledger_tps_window_seconds($pdo);
        $window = mypi_ledger_window_unix($event_unix, $w);
        $tps_uid = mypi_ledger_tps_uid($window, $w);
        $facets = json_encode(mypi_ledger_tps_facets($window));
        $pdo->prepare(
            'INSERT INTO tps_shelves(tps_uid, window_unix, window_seconds, clock_id, facets_json, created_at)
             VALUES (?,?,?,?,?,?)
             ON CONFLICT(tps_uid) DO NOTHING'
        )->execute([$tps_uid, $window, $w, 'gaia', $facets, $ingest_unix]);
        $seq = (int) $pdo->query(
            'SELECT COUNT(*) FROM tps_attach WHERE tps_uid=' . $pdo->quote($tps_uid)
        )->fetchColumn();
        $pdo->prepare(
            'INSERT INTO tps_attach(tps_uid, c_uid, kind, seq, attached_at)
             VALUES (?,?,?,?,?)
             ON CONFLICT(tps_uid, c_uid) DO NOTHING'
        )->execute([$tps_uid, $c_uid, $kind, $seq, $ingest_unix]);
        return $tps_uid;
    }

    /**
     * Write Charlie edges + gravity for a crate.
     *
     * Edges come from relationship language in tags_raw (this*rel>that).
     * Gravity bumps EVERY production tag, including auto place tags
     * (path:, @seg, sys:, dom:, mod:) — those are real Charlie material, not hidden filters.
     *
     * @param list<string>|null $all_tags  full tag list from parse_tags (preferred); if null, uses parse of tags_raw only
     * @return list<array{from:string,rel:string,to:string}>
     */
    function mypi_ledger_charlie_write(PDO $pdo, $c_uid, $tags_raw, $sys, $dom, $room, $mod, $ingest, $all_tags = null) {
        $parsed = mypi_ledger_parse_charlie($tags_raw);
        $insE = $pdo->prepare(
            'INSERT INTO thread_edges(c_uid, from_term, rel, to_term, ingest_unix, sys, dom, room, mod)
             VALUES (?,?,?,?,?,?,?,?,?)'
        );
        $bump = $pdo->prepare(
            'INSERT INTO thread_terms(term, gravity, updated_at) VALUES (?,?,?)
             ON CONFLICT(term) DO UPDATE SET
               gravity = gravity + 1,
               updated_at = excluded.updated_at'
        );
        $already = [];
        foreach ($parsed['edges'] as $e) {
            $insE->execute([
                $c_uid, $e['from'], $e['rel'], $e['to'], $ingest, $sys, $dom, $room, $mod,
            ]);
            // Gravity for a, connector, c, and full chain (connector was missing before)
            foreach ([
                $e['from'],
                $e['rel'],
                $e['to'],
                $e['from'] . '*' . $e['rel'] . '>' . $e['to'],
            ] as $term) {
                if ($term === '' || isset($already[$term])) {
                    continue;
                }
                $bump->execute([$term, 1, $ingest]);
                $already[$term] = true;
            }
        }
        // Full production tag set (user tags + auto place tags) — do not hide path:/@/sys: from Charlie
        $tagList = is_array($all_tags) ? $all_tags : $parsed['tags'];
        foreach ($tagList as $t) {
            $t = strtolower(trim((string) $t));
            if ($t === '' || isset($already[$t])) {
                continue;
            }
            // Edge full-forms already bumped; plain * in the middle of non-edge junk is still a term if present
            $bump->execute([$t, 1, $ingest]);
            $already[$t] = true;
        }
        return $parsed['edges'];
    }

    function mypi_ledger_list_tps($limit = 40) {
        $pdo = mypi_ledger_pdo();
        $limit = max(1, min(100, (int) $limit));
        return $pdo->query(
            "SELECT s.*, (SELECT COUNT(*) FROM tps_attach a WHERE a.tps_uid=s.tps_uid) AS n_crates
             FROM tps_shelves s ORDER BY s.window_unix DESC LIMIT $limit"
        )->fetchAll();
    }

    function mypi_ledger_tps_crates($tps_uid) {
        // Order by exact event time inside the window (then ingest)
        $st = mypi_ledger_pdo()->prepare(
            'SELECT c.*, a.seq, a.attached_at FROM tps_attach a
             JOIN crates c ON c.c_uid = a.c_uid
             WHERE a.tps_uid = ?
             ORDER BY c.event_unix ASC, c.ingest_unix ASC, a.seq ASC'
        );
        $st->execute([$tps_uid]);
        return $st->fetchAll();
    }

    function mypi_ledger_charlie_terms($sort = 'gravity', $limit = 100) {
        $limit = max(1, min(200, (int) $limit));
        $pdo = mypi_ledger_pdo();
        if ($sort === 'term') {
            $order = 'term ASC';
        } elseif ($sort === 'recent') {
            $order = 'updated_at DESC';
        } else {
            $order = 'gravity DESC, term ASC';
        }
        return $pdo->query(
            "SELECT term, gravity, updated_at FROM thread_terms ORDER BY $order LIMIT $limit"
        )->fetchAll();
    }

    function mypi_ledger_charlie_gravity($limit = 30) {
        $limit = max(1, min(100, (int) $limit));
        return mypi_ledger_pdo()->query(
            "SELECT term, gravity, updated_at FROM thread_terms
             ORDER BY gravity DESC, term ASC LIMIT $limit"
        )->fetchAll();
    }

    function mypi_ledger_charlie_edges($limit = 40) {
        $limit = max(1, min(100, (int) $limit));
        return mypi_ledger_pdo()->query(
            "SELECT * FROM thread_edges ORDER BY id DESC LIMIT $limit"
        )->fetchAll();
    }

    /**
     * @return array{ok:bool,c_uid?:string,tps_uid?:string,edges?:int,error?:string}
     */
    function mypi_ledger_create_post(array $in) {
        $topic = trim((string) ($in['topic'] ?? ''));
        $body = trim((string) ($in['body'] ?? ''));
        if ($topic === '' && $body === '') {
            return ['ok' => false, 'error' => 'empty post'];
        }
        $sys = (string) ($in['sys'] ?? '');
        $dom = (string) ($in['dom'] ?? '');
        $room = (string) ($in['room'] ?? '');
        $mod = (string) ($in['mod'] ?? '');
        $place_path = trim(implode('/', array_filter([$sys, $dom, $room], 'strlen')), '/');
        $place_label = (string) ($in['place_label'] ?? '');
        $tags_raw = (string) ($in['tags_raw'] ?? '');
        $agent = (string) ($in['agent'] ?? 'user');
        $tool = (string) ($in['tool'] ?? 'postBASIC');
        $kind = (string) ($in['kind'] ?? 'post');
        $actor = (string) ($in['actor'] ?? $mod ?: 'hands');
        $tz = (string) ($in['timezone'] ?? '');
        $ingest = time();
        $event = isset($in['event_unix']) && $in['event_unix'] !== '' && $in['event_unix'] !== null
            ? (int) $in['event_unix']
            : $ingest;
        $c_uid = mypi_ledger_new_cuid();
        $tags = mypi_ledger_parse_tags($tags_raw, $sys, $dom, $room, $mod);
        $pdo = mypi_ledger_pdo();
        $w = mypi_ledger_tps_window_seconds($pdo);
        $window = mypi_ledger_window_unix($event, $w);
        $tps_uid = mypi_ledger_tps_uid($window, $w);
        $pdo->prepare(
            'INSERT INTO crates(
              c_uid, kind, topic, body, agent, tool, tool_version,
              place_path, place_label, sys, dom, room, mod,
              tags_json, tags_raw, event_unix, ingest_unix, timezone, t_uid, meta_json,
              created_at, updated_at
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
        )->execute([
            $c_uid, $kind, $topic, $body, $agent, $tool, (int) ($in['tool_version'] ?? 5),
            $place_path, $place_label, $sys, $dom, $room, $mod,
            json_encode($tags), $tags_raw, $event, $ingest, $tz, $tps_uid,
            json_encode($in['meta'] ?? new stdClass()),
            $ingest, $ingest,
        ]);
        mypi_ledger_tps_ensure_and_attach($pdo, $c_uid, $kind, $event, $ingest);
        $insTag = $pdo->prepare('INSERT OR IGNORE INTO tag_map(c_uid, tag) VALUES(?,?)');
        foreach ($tags as $t) {
            $insTag->execute([$c_uid, $t]);
        }
        // Pass full tags (incl. auto path/@/sys/dom/mod) so Charlie gravity is production-complete
        $edges = mypi_ledger_charlie_write($pdo, $c_uid, $tags_raw, $sys, $dom, $room, $mod, $ingest, $tags);
        $pdo->prepare(
            'INSERT INTO crate_events(
              c_uid, event_type, payload_json, actor, place_path,
              event_unix, ingest_unix, tool
            ) VALUES (?,?,?,?,?,?,?,?)'
        )->execute([
            $c_uid,
            'create',
            json_encode([
                'topic' => $topic,
                'body' => $body,
                'tags' => $tags,
                'edges' => $edges,
                'tps_uid' => $tps_uid,
                'sys' => $sys,
                'dom' => $dom,
                'room' => $room,
                'mod' => $mod,
            ]),
            $actor,
            $place_path,
            $event,
            $ingest,
            $tool,
        ]);
        return [
            'ok' => true,
            'c_uid' => $c_uid,
            'tps_uid' => $tps_uid,
            'edges' => count($edges),
        ];
    }

    /**
     * List crates. Filters: sys, dom, room, mod, kind, tool, session (meta.session),
     * order: 'desc'|'asc' on event_unix then ingest_unix.
     *
     * @return list<array>
     */
    function mypi_ledger_list(array $opts = []) {
        $pdo = mypi_ledger_pdo();
        $limit = (int) ($opts['limit'] ?? 50);
        $sys = $opts['sys'] ?? null;
        $dom = $opts['dom'] ?? null;
        $room = $opts['room'] ?? null;
        $mod = $opts['mod'] ?? null;
        $kind = $opts['kind'] ?? null;
        $tool = $opts['tool'] ?? null;
        $session = $opts['session'] ?? null;
        $includeDeleted = !empty($opts['include_deleted']);
        $order = strtolower((string) ($opts['order'] ?? 'desc'));
        if ($order !== 'asc') {
            $order = 'desc';
        }
        $sql = 'SELECT * FROM crates WHERE 1=1';
        $args = [];
        if (!$includeDeleted) {
            $sql .= ' AND (deleted_at IS NULL OR deleted_at = 0)';
        }
        if ($sys !== null && $sys !== '') {
            $sql .= ' AND sys = ?';
            $args[] = $sys;
        }
        if ($dom !== null && $dom !== '') {
            $sql .= ' AND dom = ?';
            $args[] = $dom;
        }
        if ($room !== null && $room !== '') {
            $sql .= ' AND room = ?';
            $args[] = $room;
        }
        if ($mod !== null && $mod !== '') {
            $sql .= ' AND mod = ?';
            $args[] = $mod;
        }
        if ($kind !== null && $kind !== '') {
            $sql .= ' AND kind = ?';
            $args[] = $kind;
        }
        if ($tool !== null && $tool !== '') {
            $sql .= ' AND tool = ?';
            $args[] = $tool;
        }
        // chat sessions (and any tool that stamps meta.session)
        if ($session !== null && $session !== '') {
            $sql .= " AND json_extract(meta_json, '$.session') = ?";
            $args[] = $session;
        }
        $sql .= ' ORDER BY COALESCE(event_unix, ingest_unix) ' . strtoupper($order)
            . ', ingest_unix ' . strtoupper($order)
            . ' LIMIT ' . max(1, min(500, $limit));
        $st = $pdo->prepare($sql);
        $st->execute($args);
        return $st->fetchAll();
    }

    /**
     * Distinct chat sessions at a place (from meta.session on kind=chat crates).
     *
     * @return list<array{session:string,n:int,last_unix:int,label:string}>
     */
    function mypi_ledger_chat_sessions(array $opts = []) {
        $pdo = mypi_ledger_pdo();
        $sys = (string) ($opts['sys'] ?? '');
        $dom = (string) ($opts['dom'] ?? '');
        $room = (string) ($opts['room'] ?? '');
        $sql = "SELECT
            COALESCE(json_extract(meta_json, '$.session'), 'live') AS session,
            COALESCE(json_extract(meta_json, '$.session_label'), '') AS label,
            COUNT(*) AS n,
            MAX(COALESCE(event_unix, ingest_unix)) AS last_unix
          FROM crates
          WHERE kind = 'chat'
            AND (deleted_at IS NULL OR deleted_at = 0)";
        $args = [];
        if ($sys !== '') {
            $sql .= ' AND sys = ?';
            $args[] = $sys;
        }
        if ($dom !== '') {
            $sql .= ' AND dom = ?';
            $args[] = $dom;
        }
        if ($room !== '') {
            $sql .= ' AND room = ?';
            $args[] = $room;
        }
        $sql .= ' GROUP BY session ORDER BY last_unix DESC LIMIT 40';
        $st = $pdo->prepare($sql);
        $st->execute($args);
        return $st->fetchAll() ?: [];
    }

    /** Shared place context from SKY__AUTH constants / SITE. */
    function mypi_ledger_place_from_sky(): array {
        $sys = defined('WORLD_ID') ? WORLD_ID : (defined('SYS_ID') ? SYS_ID : (string) ($GLOBALS['SITE'] ?? ''));
        $dom = defined('DOM_SLUG') ? DOM_SLUG : '';
        $room = defined('ROOM_SLUG') ? ROOM_SLUG : '';
        $mod = defined('MOD_SLUG') ? MOD_SLUG : '';
        $place_label = defined('ROOM_DISPLAY') ? ROOM_DISPLAY : '';
        return compact('sys', 'dom', 'room', 'mod', 'place_label');
    }

    function mypi_ledger_get($c_uid) {
        $st = mypi_ledger_pdo()->prepare('SELECT * FROM crates WHERE c_uid = ?');
        $st->execute([$c_uid]);
        $row = $st->fetch();
        return $row ?: null;
    }

    /**
     * Crates tagged with this Charlie/production tag (via tag_map).
     *
     * @return list<array>
     */
    function mypi_ledger_crates_for_tag(string $tag, int $limit = 80) {
        $tag = strtolower(trim($tag));
        if ($tag === '') {
            return [];
        }
        $limit = max(1, min(200, $limit));
        $st = mypi_ledger_pdo()->prepare(
            'SELECT c.* FROM crates c
             JOIN tag_map t ON t.c_uid = c.c_uid
             WHERE t.tag = ?
               AND (c.deleted_at IS NULL OR c.deleted_at = 0)
             ORDER BY COALESCE(c.event_unix, c.ingest_unix) DESC
             LIMIT ' . $limit
        );
        $st->execute([$tag]);
        return $st->fetchAll() ?: [];
    }

    function mypi_ledger_history($c_uid) {
        $st = mypi_ledger_pdo()->prepare(
            'SELECT * FROM crate_events WHERE c_uid = ? ORDER BY id ASC'
        );
        $st->execute([$c_uid]);
        return $st->fetchAll();
    }

    /**
     * Soft-delete: hides from lists, keeps row + history. Can restore.
     * @return array{ok:bool,error?:string}
     */
    function mypi_ledger_soft_delete($c_uid, $actor = 'hands', $tool = 'ledger') {
        $pdo = mypi_ledger_pdo();
        $row = mypi_ledger_get($c_uid);
        if (!$row) {
            return ['ok' => false, 'error' => 'not found'];
        }
        if (!empty($row['deleted_at'])) {
            return ['ok' => true]; // already gone from view
        }
        $now = time();
        $pdo->prepare(
            'UPDATE crates SET deleted_at = ?, updated_at = ? WHERE c_uid = ?'
        )->execute([$now, $now, $c_uid]);
        $pdo->prepare(
            'INSERT INTO crate_events(
              c_uid, event_type, payload_json, actor, place_path,
              event_unix, ingest_unix, tool
            ) VALUES (?,?,?,?,?,?,?,?)'
        )->execute([
            $c_uid,
            'soft_delete',
            json_encode(['topic' => $row['topic'] ?? '']),
            $actor,
            $row['place_path'] ?? '',
            $now,
            $now,
            $tool,
        ]);
        $pdo->prepare(
            'INSERT INTO deleted_log(c_uid, snapshot_json, deleted_at, actor, hard)
             VALUES (?,?,?,?,0)'
        )->execute([$c_uid, json_encode($row), $now, $actor]);
        return ['ok' => true];
    }

    /**
     * Hard-delete: removes crate, tags, events. Snapshot kept in deleted_log only.
     * Use when soft-delete is not enough. Does NOT nuke the world — one c_uid only.
     * @return array{ok:bool,error?:string}
     */
    function mypi_ledger_hard_delete($c_uid, $actor = 'hands', $tool = 'ledger') {
        $pdo = mypi_ledger_pdo();
        $row = mypi_ledger_get($c_uid);
        if (!$row) {
            return ['ok' => false, 'error' => 'not found'];
        }
        $now = time();
        $pdo->prepare(
            'INSERT INTO deleted_log(c_uid, snapshot_json, deleted_at, actor, hard)
             VALUES (?,?,?,?,1)'
        )->execute([$c_uid, json_encode($row), $now, $actor]);
        $pdo->prepare('DELETE FROM tag_map WHERE c_uid = ?')->execute([$c_uid]);
        $pdo->prepare('DELETE FROM crate_events WHERE c_uid = ?')->execute([$c_uid]);
        $pdo->prepare('DELETE FROM crates WHERE c_uid = ?')->execute([$c_uid]);
        return ['ok' => true];
    }

    function mypi_ledger_restore($c_uid, $actor = 'hands', $tool = 'ledger') {
        $pdo = mypi_ledger_pdo();
        $row = mypi_ledger_get($c_uid);
        if (!$row) {
            return ['ok' => false, 'error' => 'not found'];
        }
        if (empty($row['deleted_at'])) {
            return ['ok' => true];
        }
        $now = time();
        $pdo->prepare(
            'UPDATE crates SET deleted_at = NULL, updated_at = ? WHERE c_uid = ?'
        )->execute([$now, $c_uid]);
        $pdo->prepare(
            'INSERT INTO crate_events(
              c_uid, event_type, payload_json, actor, place_path,
              event_unix, ingest_unix, tool
            ) VALUES (?,?,?,?,?,?,?,?)'
        )->execute([
            $c_uid, 'restore', '{}', $actor, $row['place_path'] ?? '',
            $now, $now, $tool,
        ]);
        return ['ok' => true];
    }
}
