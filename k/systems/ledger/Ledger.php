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

    /**
     * Strip invisible / bidi junk Windows and rich-text paste inject
     * (LTR marks around "September ‎16 ‎2025 ‏‎10:16PM", etc.).
     */
    function mypi_sanitize_datetime_text(string $s): string
    {
        // Unicode format / bidi / zero-width / BOM
        $s = preg_replace('/[\x{200B}-\x{200F}\x{202A}-\x{202E}\x{2060}-\x{206F}\x{FEFF}\x{00AD}]/u', '', $s) ?? $s;
        // weird spaces → normal space
        $s = preg_replace('/[\x{00A0}\x{202F}\x{2000}-\x{200A}\x{3000}]/u', ' ', $s) ?? $s;
        // normalize am/pm glued to digits
        $s = preg_replace('/(\d)\s*([ap])\.?\s*m\.?/i', '$1 $2m', $s) ?? $s;
        $s = preg_replace('/\bat\b/i', ' ', $s) ?? $s;
        // drop weekday names (Tuesday,) — strtotime/DateTime handle better without sometimes
        $s = preg_replace(
            '/\b(monday|tuesday|wednesday|thursday|friday|saturday|sunday|mon|tue|wed|thu|fri|sat|sun)\b[, ]*/i',
            '',
            $s
        ) ?? $s;
        $s = str_replace([',', '·', '—', '–'], ' ', $s);
        $s = preg_replace('/\s+/', ' ', trim($s)) ?? $s;
        return $s;
    }

    /**
     * Parse free-text datetime → unix seconds (nearest second).
     * Accepts: bare unix, "now", ISO, US dates, "September 16 2025 10:16PM",
     * "09/16/2025 10:16pm", weekday-prefixed dumps, Windows LTR-mark garbage, etc.
     * Empty → null (caller uses "now").
     *
     * @return int|null
     */
    function mypi_parse_event_time($raw, $timezone = '') {
        if ($raw === null) {
            return null;
        }
        $s = mypi_sanitize_datetime_text((string) $raw);
        if ($s === '') {
            return null;
        }
        if (preg_match('/^(now|today|n)$/i', $s)) {
            return time();
        }
        // pure integer unix (9–13 digits)
        if (preg_match('/^\d{9,13}$/', $s)) {
            $n = (int) $s;
            if ($n > 9999999999) {
                $n = (int) floor($n / 1000);
            }
            return $n;
        }

        $tzName = trim((string) $timezone);
        $tzObj = null;
        if ($tzName !== '') {
            try {
                $tzObj = new DateTimeZone($tzName);
            } catch (Throwable $e) {
                $tzObj = null;
            }
        }
        if ($tzObj === null) {
            try {
                $tzObj = new DateTimeZone(date_default_timezone_get());
            } catch (Throwable $e) {
                $tzObj = new DateTimeZone('UTC');
            }
        }

        $try = $s;
        // "10:16PM" already normalized; unify to "10:16 PM" for formats with A
        $try = preg_replace_callback('/\b([ap])m\b/i', static function ($m) {
            return strtoupper($m[1]) . 'M';
        }, $try) ?? $try;

        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'Y-m-d g:i A',
            'Y-m-d g:iA',
            'Y-m-d',
            'm/d/Y H:i:s',
            'm/d/Y H:i',
            'm/d/Y g:i:s A',
            'm/d/Y g:i A',
            'm/d/Y g:iA',
            'm/d/Y',
            'n/j/Y g:i:s A',
            'n/j/Y g:i A',
            'n/j/Y H:i',
            'n/j/Y',
            'm/d/y g:i A',
            'm/d/y H:i',
            'm/d/y',
            'M j Y g:i:s A',
            'M j Y g:i A',
            'M j Y g:iA',
            'M j Y H:i:s',
            'M j Y H:i',
            'M j Y',
            'F j Y g:i:s A',
            'F j Y g:i A',
            'F j Y g:iA',
            'F j Y H:i:s',
            'F j Y H:i',
            'F j Y',
            'F j, Y g:i:s A',
            'F j, Y g:i A',
            'F j, Y',
            'j M Y g:i A',
            'j M Y H:i',
            'j M Y',
            DateTimeInterface::ATOM,
            DateTimeInterface::RFC2822,
            'c',
        ];

        $ts = null;
        foreach ($formats as $fmt) {
            if (!is_string($fmt) || $fmt === '') {
                continue;
            }
            $dt = DateTime::createFromFormat('!' . $fmt, $try, $tzObj);
            if (!$dt instanceof DateTime) {
                continue;
            }
            $errs = DateTime::getLastErrors();
            if (is_array($errs) && (!empty($errs['error_count']) || !empty($errs['warning_count']))) {
                continue;
            }
            $ts = $dt->getTimestamp();
            break;
        }

        if ($ts === null) {
            try {
                $dt2 = new DateTime($try, $tzObj);
                $ts = $dt2->getTimestamp();
            } catch (Throwable $e) {
                $prevTz = date_default_timezone_get();
                try {
                    date_default_timezone_set($tzObj->getName());
                } catch (Throwable $e2) {
                }
                $parsed = strtotime($try);
                date_default_timezone_set($prevTz);
                if ($parsed !== false) {
                    $ts = (int) $parsed;
                }
            }
        }

        return $ts !== null ? (int) $ts : null;
    }

    /** Single-depth folder name for fileKeeper (no slashes; empty = root). */
    function mypi_ledger_file_folder_norm(string $name): string
    {
        $name = trim($name);
        // strip path junk — one layer only
        $name = str_replace(['\\', '/'], ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name) ?? $name;
        $name = trim($name);
        if (function_exists('mb_substr')) {
            $name = mb_substr($name, 0, 80);
        } else {
            $name = substr($name, 0, 80);
        }
        return $name;
    }

    /**
     * Distinct folders at a place (from file heads + empty folder markers).
     *
     * @return list<string> sorted, never includes ''
     */
    function mypi_ledger_file_folders(array $opts = []): array
    {
        $pdo = mypi_ledger_pdo();
        $sys = (string) ($opts['sys'] ?? '');
        $dom = (string) ($opts['dom'] ?? '');
        $room = (string) ($opts['room'] ?? '');
        $sql = "SELECT DISTINCT TRIM(COALESCE(json_extract(meta_json, '$.folder'), '')) AS folder
          FROM crates
          WHERE tool = 'fileKeeper'
            AND kind IN ('file', 'folder')
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
        $st = $pdo->prepare($sql);
        $st->execute($args);
        $out = [];
        foreach ($st->fetchAll() ?: [] as $row) {
            $f = mypi_ledger_file_folder_norm((string) ($row['folder'] ?? ''));
            if ($f !== '') {
                $out[$f] = true;
            }
        }
        // also folder markers use topic as name
        $sql2 = "SELECT topic FROM crates
          WHERE tool = 'fileKeeper' AND kind = 'folder'
            AND (deleted_at IS NULL OR deleted_at = 0)";
        $args2 = [];
        if ($sys !== '') {
            $sql2 .= ' AND sys = ?';
            $args2[] = $sys;
        }
        if ($dom !== '') {
            $sql2 .= ' AND dom = ?';
            $args2[] = $dom;
        }
        if ($room !== '') {
            $sql2 .= ' AND room = ?';
            $args2[] = $room;
        }
        $st2 = $pdo->prepare($sql2);
        $st2->execute($args2);
        foreach ($st2->fetchAll() ?: [] as $row) {
            $f = mypi_ledger_file_folder_norm((string) ($row['topic'] ?? ''));
            if ($f !== '') {
                $out[$f] = true;
            }
        }
        $list = array_keys($out);
        natcasesort($list);
        return array_values($list);
    }

    /**
     * Create an empty folder marker (kind=folder) so it appears with no files yet.
     *
     * @return array{ok:bool,folder?:string,error?:string}
     */
    function mypi_ledger_file_mkdir(array $in): array
    {
        $folder = mypi_ledger_file_folder_norm((string) ($in['folder'] ?? $in['title'] ?? ''));
        if ($folder === '') {
            return ['ok' => false, 'error' => 'empty folder name'];
        }
        $sys = (string) ($in['sys'] ?? '');
        $dom = (string) ($in['dom'] ?? '');
        $room = (string) ($in['room'] ?? '');
        $mod = (string) ($in['mod'] ?? '');
        $place_path = trim(implode('/', array_filter([$sys, $dom, $room], 'strlen')), '/');
        $agent = (string) ($in['agent'] ?? $mod ?: 'user');
        $ingest = time();
        $c_uid = mypi_ledger_new_cuid();
        $pdo = mypi_ledger_pdo();
        $w = mypi_ledger_tps_window_seconds($pdo);
        $window = mypi_ledger_window_unix($ingest, $w);
        $tps_uid = mypi_ledger_tps_uid($window, $w);
        $meta = ['folder' => $folder];
        $pdo->prepare(
            'INSERT INTO crates(
              c_uid, kind, topic, body, agent, tool, tool_version,
              place_path, place_label, sys, dom, room, mod,
              tags_json, tags_raw, event_unix, ingest_unix, timezone, t_uid, meta_json,
              created_at, updated_at
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
        )->execute([
            $c_uid, 'folder', $folder, '', $agent, 'fileKeeper', 1,
            $place_path, (string) ($in['place_label'] ?? ''), $sys, $dom, $room, $mod,
            '[]', '', $ingest, $ingest, (string) ($in['timezone'] ?? ''), $tps_uid,
            json_encode($meta),
            $ingest, $ingest,
        ]);
        mypi_ledger_tps_ensure_and_attach($pdo, $c_uid, 'folder', $ingest, $ingest);
        return ['ok' => true, 'folder' => $folder, 'c_uid' => $c_uid];
    }

    /**
     * fileKeeper: save a markdown file revision.
     * New file → stem_c_uid = this c_uid, parent_c_uid empty.
     * Edit → new c_uid, stem preserved, parent = previous head.
     * Lineage is structural (meta), not Charlie tags.
     *
     * @return array{ok:bool,c_uid?:string,stem_c_uid?:string,parent_c_uid?:string,error?:string}
     */
    function mypi_ledger_file_save(array $in) {
        $title = trim((string) ($in['title'] ?? $in['topic'] ?? ''));
        $body = (string) ($in['body'] ?? '');
        if ($title === '' && trim($body) === '') {
            return ['ok' => false, 'error' => 'empty file'];
        }
        if ($title === '') {
            $title = 'untitled';
        }

        $parent = trim((string) ($in['parent_c_uid'] ?? ''));
        $stem = trim((string) ($in['stem_c_uid'] ?? ''));
        $tags_raw = trim((string) ($in['tags_raw'] ?? ''));

        if ($parent !== '') {
            $prev = mypi_ledger_get($parent);
            if (!$prev) {
                return ['ok' => false, 'error' => 'parent not found'];
            }
            $prevMeta = json_decode((string) ($prev['meta_json'] ?? '{}'), true) ?: [];
            $stem = (string) ($prevMeta['stem_c_uid'] ?? $prev['c_uid']);
        }

        // pre-allocate stem for brand-new files
        $c_uid_preview = mypi_ledger_new_cuid();
        if ($stem === '') {
            $stem = $c_uid_preview;
        }

        // one-depth folder ('' = root). Explicit key wins; else inherit parent.
        $folder = '';
        if (array_key_exists('folder', $in)) {
            $folder = mypi_ledger_file_folder_norm((string) $in['folder']);
        } elseif ($parent !== '') {
            $prev0 = mypi_ledger_get($parent);
            $prev0Meta = json_decode((string) ($prev0['meta_json'] ?? '{}'), true) ?: [];
            $folder = mypi_ledger_file_folder_norm((string) ($prev0Meta['folder'] ?? ''));
        }

        $meta = array_merge(is_array($in['meta'] ?? null) ? $in['meta'] : [], [
            'stem_c_uid' => $stem,
            'parent_c_uid' => $parent,
            'rev' => (int) ($in['rev'] ?? 0),
            'folder' => $folder,
        ]);
        if ($parent !== '') {
            $prev = mypi_ledger_get($parent);
            $prevMeta = json_decode((string) ($prev['meta_json'] ?? '{}'), true) ?: [];
            $meta['rev'] = (int) ($prevMeta['rev'] ?? 1) + 1;
        } else {
            $meta['rev'] = 1;
        }

        // inject chosen c_uid via create path — create_post always mints; so call lower-level style
        $sys = (string) ($in['sys'] ?? '');
        $dom = (string) ($in['dom'] ?? '');
        $room = (string) ($in['room'] ?? '');
        $mod = (string) ($in['mod'] ?? '');
        $place_path = trim(implode('/', array_filter([$sys, $dom, $room], 'strlen')), '/');
        $place_label = (string) ($in['place_label'] ?? '');
        $agent = (string) ($in['agent'] ?? $mod ?: 'user');
        $tool = 'fileKeeper';
        $kind = 'file';
        $actor = (string) ($in['actor'] ?? $mod ?: 'hands');
        $tz = (string) ($in['timezone'] ?? '');
        $ingest = time();
        if (array_key_exists('event_unix', $in) && $in['event_unix'] !== '' && $in['event_unix'] !== null) {
            $event = (int) $in['event_unix'];
        } else {
            $event = $ingest;
        }
        // never trust 0 as a real event unless explicitly ancient; treat 0 as missing
        if ($event <= 0) {
            $event = $ingest;
        }

        $c_uid = $c_uid_preview;
        // first revision: stem is self
        if ($parent === '') {
            $stem = $c_uid;
            $meta['stem_c_uid'] = $stem;
            $meta['parent_c_uid'] = '';
        }
        $meta['event_unix'] = $event;
        $meta['event_raw'] = (string) ($in['event_raw'] ?? '');

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
            $c_uid, $kind, $title, $body, $agent, $tool, (int) ($in['tool_version'] ?? 1),
            $place_path, $place_label, $sys, $dom, $room, $mod,
            json_encode($tags), $tags_raw, $event, $ingest, $tz, $tps_uid,
            json_encode($meta),
            $ingest, $ingest,
        ]);
        mypi_ledger_tps_ensure_and_attach($pdo, $c_uid, $kind, $event, $ingest);
        $insTag = $pdo->prepare('INSERT OR IGNORE INTO tag_map(c_uid, tag) VALUES(?,?)');
        foreach ($tags as $t) {
            $insTag->execute([$c_uid, $t]);
        }
        $edges = mypi_ledger_charlie_write($pdo, $c_uid, $tags_raw, $sys, $dom, $room, $mod, $ingest, $tags);
        $pdo->prepare(
            'INSERT INTO crate_events(
              c_uid, event_type, payload_json, actor, place_path,
              event_unix, ingest_unix, tool
            ) VALUES (?,?,?,?,?,?,?,?)'
        )->execute([
            $c_uid,
            $parent === '' ? 'file_create' : 'file_revise',
            json_encode([
                'topic' => $title,
                'stem_c_uid' => $stem,
                'parent_c_uid' => $parent,
                'rev' => $meta['rev'],
                'edges' => $edges,
                'tps_uid' => $tps_uid,
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
            'stem_c_uid' => $stem,
            'parent_c_uid' => $parent,
            'rev' => $meta['rev'],
            'tps_uid' => $tps_uid,
        ];
    }

    /**
     * Latest revision of each file stem at a place (kind=file, tool=fileKeeper).
     *
     * @return list<array>
     */
    function mypi_ledger_file_heads(array $opts = []) {
        $pdo = mypi_ledger_pdo();
        $limit = max(1, min(200, (int) ($opts['limit'] ?? 80)));
        $sys = (string) ($opts['sys'] ?? '');
        $dom = (string) ($opts['dom'] ?? '');
        $room = (string) ($opts['room'] ?? '');
        $sql = "SELECT c.* FROM crates c
          INNER JOIN (
            SELECT
              COALESCE(json_extract(meta_json, '$.stem_c_uid'), c_uid) AS stem,
              MAX(ingest_unix) AS mx
            FROM crates
            WHERE kind = 'file'
              AND tool = 'fileKeeper'
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
        $sql .= " GROUP BY stem
          ) h ON COALESCE(json_extract(c.meta_json, '$.stem_c_uid'), c.c_uid) = h.stem
              AND c.ingest_unix = h.mx
          WHERE c.kind = 'file' AND c.tool = 'fileKeeper'
            AND (c.deleted_at IS NULL OR c.deleted_at = 0)
          ORDER BY c.ingest_unix DESC
          LIMIT " . $limit;
        $st = $pdo->prepare($sql);
        $st->execute($args);
        return $st->fetchAll() ?: [];
    }

    /**
     * All revisions for a stem (oldest → newest).
     *
     * @return list<array>
     */
    function mypi_ledger_file_revisions(string $stem_c_uid, int $limit = 50) {
        $stem_c_uid = trim($stem_c_uid);
        if ($stem_c_uid === '') {
            return [];
        }
        $limit = max(1, min(100, $limit));
        $st = mypi_ledger_pdo()->prepare(
            "SELECT * FROM crates
             WHERE kind = 'file' AND tool = 'fileKeeper'
               AND (deleted_at IS NULL OR deleted_at = 0)
               AND (
                 c_uid = ?
                 OR json_extract(meta_json, '$.stem_c_uid') = ?
               )
             ORDER BY ingest_unix ASC
             LIMIT " . $limit
        );
        $st->execute([$stem_c_uid, $stem_c_uid]);
        return $st->fetchAll() ?: [];
    }
}
