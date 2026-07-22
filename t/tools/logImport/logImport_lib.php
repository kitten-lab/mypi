<?php
/**
 * logImport helpers — tree-core catalog + immutable shard load.
 * Catalog: z/logs/tree_cores/catalog.json (from ledger/tree_core_catalog.py)
 */

if (!function_exists('logimport_paths')) {
    function logimport_paths(): array {
        $root = rtrim(str_replace('\\', '/', echoSONAR), '/');
        return [
            'ot' => $root . '/z/conversations.json',
            'nt' => $root . '/z/logs/NEW_MASTER_USE_THESE',
            'catalog' => $root . '/z/logs/tree_cores/catalog.json',
            'glass' => $root . '/z/logs/tree_cores/glass',
            'wip' => $root . '/z/logs/tree_cores/wip',
        ];
    }

    function logimport_load_catalog(): ?array {
        static $cache = null;
        static $loaded = false;
        if ($loaded) {
            return $cache;
        }
        $loaded = true;
        $p = logimport_paths()['catalog'];
        if (!is_file($p)) {
            $cache = null;
            return null;
        }
        // catalog is metadata only (~few MB); still avoid double-read per request
        $j = json_decode((string) file_get_contents($p), true);
        $cache = is_array($j) ? $j : null;
        return $cache;
    }

    function logimport_safe_glass_name(string $conversationId): string {
        $s = preg_replace('/[^a-zA-Z0-9_-]/', '_', $conversationId) ?? '';
        $s = substr($s, 0, 120);
        return $s !== '' ? $s : 'unknown';
    }

    function logimport_face_key($face): string {
        $s = trim((string) $face);
        if ($s === '') {
            return '';
        }
        if (ctype_digit($s)) {
            return sprintf('%03d', (int) $s);
        }
        return $s;
    }

    function logimport_core_by_face($face): ?array {
        $cat = logimport_load_catalog();
        if (!$cat) {
            return null;
        }
        $key = logimport_face_key($face);
        $n = ctype_digit(trim((string) $face)) ? (int) $face : null;
        foreach ($cat['cores'] ?? [] as $c) {
            if (!is_array($c)) {
                continue;
            }
            if (($c['face_id'] ?? '') === $key) {
                return $c;
            }
            if ($n !== null && (int) ($c['n'] ?? 0) === $n) {
                return $c;
            }
        }
        return null;
    }

    function logimport_load_conversation(array $core): ?array {
        $paths = logimport_paths();
        $cid = (string) ($core['conversation_id'] ?? '');
        if ($cid === '') {
            return null;
        }

        // Preferred: one-chat glass extract (never load 230MB OT or 50MB NT shard)
        $glassDir = $paths['glass'];
        $candidates = [];
        if (!empty($core['glass_basename'])) {
            $candidates[] = $glassDir . '/' . $core['glass_basename'];
        }
        $candidates[] = $glassDir . '/' . logimport_safe_glass_name($cid) . '.json';
        foreach ($candidates as $gp) {
            if (is_file($gp)) {
                $one = json_decode((string) file_get_contents($gp), true);
                return is_array($one) ? $one : null;
            }
        }

        // No glass extract — refuse to load full OT (OOM). Ask operator to rebuild catalog.
        $exportKey = (string) ($core['load_export_key'] ?? $core['export_key'] ?? 'nt');
        if ($exportKey === 'ot') {
            error_log('logImport: missing glass extract for OT core ' . $cid . ' — run python ledger/tree_core_catalog.py');
            return null;
        }

        // NT shard fallback (smaller risk than OT, still heavy — prefer glass)
        $cat = logimport_load_catalog();
        $export = $paths['nt'];
        if (is_array($cat) && !empty($cat['sources']['nt']['path']) && is_dir($cat['sources']['nt']['path'])) {
            $export = rtrim(str_replace('\\', '/', (string) $cat['sources']['nt']['path']), '/');
        }
        $shardName = (string) ($core['load_shard'] ?? $core['shard'] ?? '');
        $shard = $export . '/' . $shardName;
        if (!is_file($shard)) {
            return null;
        }
        $data = json_decode((string) file_get_contents($shard), true);
        if (!is_array($data)) {
            return null;
        }
        foreach ($data as $conv) {
            if (!is_array($conv)) {
                continue;
            }
            $id = (string) ($conv['conversation_id'] ?? $conv['id'] ?? '');
            if ($id === $cid) {
                return $conv;
            }
        }
        return null;
    }

    /**
     * ChatGPT parts may be strings OR structured objects (images, etc.).
     * Never cast arrays to string (PHP 8+ warning / noise).
     */
    function logimport_part_to_text($part): string {
        if ($part === null) {
            return '';
        }
        if (is_string($part)) {
            return $part;
        }
        if (is_int($part) || is_float($part) || is_bool($part)) {
            return (string) $part;
        }
        if (!is_array($part)) {
            return '';
        }
        // Common structured part shapes in exports
        $ctype = (string) ($part['content_type'] ?? $part['type'] ?? '');
        if ($ctype === 'image_asset_pointer' || isset($part['asset_pointer'])) {
            $ptr = (string) ($part['asset_pointer'] ?? '');
            $h = (int) ($part['height'] ?? 0);
            $w = (int) ($part['width'] ?? 0);
            $dim = ($w > 0 && $h > 0) ? " {$w}×{$h}" : '';
            return '[image' . $dim . ($ptr !== '' ? ' · ' . $ptr : '') . ']';
        }
        if ($ctype === 'audio_asset_pointer' || isset($part['audio_asset_pointer'])) {
            return '[audio]';
        }
        if (isset($part['text']) && is_string($part['text'])) {
            return $part['text'];
        }
        if (isset($part['value']) && is_string($part['value'])) {
            return $part['value'];
        }
        // nested parts
        if (isset($part['parts']) && is_array($part['parts'])) {
            $bits = [];
            foreach ($part['parts'] as $sub) {
                $t = logimport_part_to_text($sub);
                if ($t !== '') {
                    $bits[] = $t;
                }
            }
            return implode("\n", $bits);
        }
        // last resort: compact JSON label, not a full dump
        if ($ctype !== '') {
            return '[' . $ctype . ']';
        }
        return '[attachment]';
    }

    /**
     * @return list<array{seq:int,message_id:string,role:string,create_time:?float,text:string}>
     */
    function logimport_extract_messages(array $conv): array {
        $mapping = $conv['mapping'] ?? null;
        if (!is_array($mapping)) {
            return [];
        }
        $rows = [];
        foreach ($mapping as $mid => $node) {
            if (!is_array($node)) {
                continue;
            }
            $msg = $node['message'] ?? null;
            if (!is_array($msg)) {
                continue;
            }
            $author = is_array($msg['author'] ?? null) ? $msg['author'] : [];
            $role = strtolower((string) ($author['role'] ?? ''));
            if ($role !== 'user' && $role !== 'assistant') {
                continue;
            }
            $content = is_array($msg['content'] ?? null) ? $msg['content'] : [];
            $parts = $content['parts'] ?? [];
            if (!is_array($parts)) {
                // some messages use content as plain string
                if (is_string($content) && trim($content) !== '') {
                    $parts = [$content];
                } else {
                    continue;
                }
            }
            $texts = [];
            foreach ($parts as $p) {
                $s = logimport_part_to_text($p);
                if (trim($s) !== '') {
                    $texts[] = $s;
                }
            }
            if (!$texts) {
                continue;
            }
            $ct = $msg['create_time'] ?? null;
            $create = is_numeric($ct) ? (float) $ct : null;
            $rows[] = [
                'message_id' => (string) $mid,
                'role' => $role,
                'create_time' => $create,
                'text' => implode("\n", $texts),
            ];
        }
        usort($rows, static function ($a, $b) {
            $an = $a['create_time'] === null ? 1 : 0;
            $bn = $b['create_time'] === null ? 1 : 0;
            if ($an !== $bn) {
                return $an - $bn;
            }
            $ac = $a['create_time'] ?? 0.0;
            $bc = $b['create_time'] ?? 0.0;
            if ($ac == $bc) {
                return strcmp($a['message_id'], $b['message_id']);
            }
            return $ac <=> $bc;
        });
        foreach ($rows as $i => &$r) {
            $r['seq'] = $i;
        }
        unset($r);
        return $rows;
    }

    function logimport_wip_path(string $face): string {
        $key = logimport_face_key($face);
        $dir = logimport_paths()['wip'];
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        return $dir . '/wip_' . $key . '.json';
    }

    function logimport_wip_load(string $face): ?array {
        $p = logimport_wip_path($face);
        if (!is_file($p)) {
            return null;
        }
        $j = json_decode((string) file_get_contents($p), true);
        return is_array($j) ? $j : null;
    }

    function logimport_wip_save(string $face, array $wip): bool {
        $p = logimport_wip_path($face);
        $wip['face_id'] = logimport_face_key($face);
        $wip['saved_at'] = time();
        return file_put_contents(
            $p,
            json_encode($wip, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        ) !== false;
    }

    /**
     * List all WIP sidecars (one file per core you've touched).
     *
     * @return list<array{face_id:string,yard_title:string,notes_preview:string,n_segments:int,saved_at:int,glass_title:string,testament_tag:string}>
     */
    function logimport_list_wips(): array {
        $dir = logimport_paths()['wip'];
        if (!is_dir($dir)) {
            return [];
        }
        $cat = logimport_load_catalog();
        $byFace = [];
        if (is_array($cat)) {
            foreach ($cat['cores'] ?? [] as $c) {
                if (is_array($c) && !empty($c['face_id'])) {
                    $byFace[(string) $c['face_id']] = $c;
                }
            }
        }
        $out = [];
        foreach (glob($dir . '/wip_*.json') ?: [] as $file) {
            $base = basename($file);
            if (!preg_match('/^wip_([0-9A-Za-z_-]+)\.json$/', $base, $m)) {
                continue;
            }
            $face = logimport_face_key($m[1]);
            $j = json_decode((string) file_get_contents($file), true);
            if (!is_array($j)) {
                continue;
            }
            $core = $byFace[$face] ?? null;
            $segs = is_array($j['segments'] ?? null) ? $j['segments'] : [];
            $notes = trim((string) ($j['notes'] ?? ''));
            $preview = $notes;
            if (function_exists('mb_substr')) {
                $preview = mb_substr($notes, 0, 80);
            } else {
                $preview = substr($notes, 0, 80);
            }
            if (strlen($notes) > 80) {
                $preview .= '…';
            }
            $out[] = [
                'face_id' => $face,
                'yard_title' => trim((string) ($j['yard_title'] ?? '')),
                'notes_preview' => $preview,
                'n_segments' => count($segs),
                'saved_at' => (int) ($j['saved_at'] ?? filemtime($file) ?: 0),
                'glass_title' => is_array($core) ? (string) ($core['title'] ?? '') : '',
                'testament_tag' => is_array($core) ? (string) ($core['testament_tag'] ?? '') : '',
            ];
        }
        usort($out, static fn($a, $b) => ($b['saved_at'] <=> $a['saved_at']));
        return $out;
    }

    /**
     * Submitted exports live here later (submit → material). Empty until then.
     *
     * @return list<array{face_id:string,path:string,saved_at:int,title:string}>
     */
    function logimport_list_exports(): array {
        $root = rtrim(str_replace('\\', '/', echoSONAR), '/');
        $dir = $root . '/z/logs/tree_cores/exports';
        if (!is_dir($dir)) {
            return [];
        }
        $out = [];
        foreach (glob($dir . '/*.json') ?: [] as $file) {
            $j = json_decode((string) file_get_contents($file), true);
            $meta = is_array($j) ? $j : [];
            $out[] = [
                'face_id' => (string) ($meta['face_id'] ?? ''),
                'path' => $file,
                'saved_at' => (int) ($meta['exported_at'] ?? filemtime($file) ?: 0),
                'title' => (string) ($meta['yard_title'] ?? $meta['title'] ?? basename($file)),
            ];
        }
        usort($out, static fn($a, $b) => ($b['saved_at'] <=> $a['saved_at']));
        return $out;
    }

    /**
     * Merge form fields into existing WIP (preserves encodes/redactions).
     */
    function logimport_wip_merge_form(string $face, array $post, ?array $existing = null): array {
        $wip = is_array($existing) ? $existing : (logimport_wip_load($face) ?: []);
        if (array_key_exists('yard_title', $post)) {
            $wip['yard_title'] = trim((string) $post['yard_title']);
        }
        if (array_key_exists('notes', $post)) {
            $wip['notes'] = (string) $post['notes'];
        }
        if (!isset($wip['encodes']) || !is_array($wip['encodes'])) {
            $wip['encodes'] = [];
        }
        if (!isset($wip['redactions']) || !is_array($wip['redactions'])) {
            $wip['redactions'] = [];
        }
        if (!isset($wip['segments']) || !is_array($wip['segments'])) {
            $wip['segments'] = [];
        }
        return $wip;
    }

    /**
     * Build working segment list from POST titles + WIP ranges (same form submit).
     *
     * @return list<array{title:string,from_seq:int,to_seq:int}>
     */
    function logimport_segments_from_post_and_wip(array $post, array $wip, int $lastSeq): array {
        $segs = logimport_segments_normalize($wip['segments'] ?? [], $lastSeq);
        if ($segs === []) {
            return [];
        }
        if (isset($post['seg_title']) && is_array($post['seg_title'])) {
            foreach ($segs as $i => $seg) {
                if (array_key_exists($i, $post['seg_title'])) {
                    $segs[$i]['title'] = trim((string) $post['seg_title'][$i]);
                }
            }
        }
        return $segs;
    }

    /**
     * Normalize segment list; empty = whole log (no cuts).
     *
     * @param list<array|mixed> $segments
     * @return list<array{title:string,from_seq:int,to_seq:int}>
     */
    function logimport_segments_normalize($segments, int $lastSeq): array {
        if (!is_array($segments) || $segments === []) {
            return [];
        }
        $out = [];
        foreach ($segments as $s) {
            if (!is_array($s)) {
                continue;
            }
            $from = (int) ($s['from_seq'] ?? 0);
            $to = (int) ($s['to_seq'] ?? $lastSeq);
            if ($from < 0) {
                $from = 0;
            }
            if ($lastSeq >= 0 && $to > $lastSeq) {
                $to = $lastSeq;
            }
            if ($to < $from) {
                continue;
            }
            $out[] = [
                'title' => trim((string) ($s['title'] ?? '')),
                'from_seq' => $from,
                'to_seq' => $to,
            ];
        }
        usort($out, static fn($a, $b) => $a['from_seq'] <=> $b['from_seq']);
        return $out;
    }

    /** Single full-span empty title → treat as no splits. */
    function logimport_segments_collapse_trivial(array $segments, int $lastSeq): array {
        if (count($segments) === 1
            && (int) $segments[0]['from_seq'] === 0
            && (int) $segments[0]['to_seq'] === $lastSeq
            && trim((string) $segments[0]['title']) === ''
        ) {
            return [];
        }
        return $segments;
    }

    /**
     * Cut points = last seq of each segment except the final one.
     *
     * @param list<array{from_seq:int,to_seq:int}> $segments
     * @return list<int>
     */
    function logimport_segment_cuts(array $segments): array {
        if (count($segments) < 2) {
            return [];
        }
        $cuts = [];
        $n = count($segments);
        for ($i = 0; $i < $n - 1; $i++) {
            $cuts[] = (int) $segments[$i]['to_seq'];
        }
        return $cuts;
    }

    /**
     * Build segments from cuts; inherit titles from $oldSegments by range overlap.
     * First fragment of a named segment keeps the name; later fragments stay empty
     * (user can name them) — never force "part N" over a saved name.
     *
     * @param list<int> $cuts
     * @param list<array{title:string,from_seq:int,to_seq:int}> $oldSegments
     * @return list<array{title:string,from_seq:int,to_seq:int}>
     */
    function logimport_segments_from_cuts(array $cuts, int $lastSeq, array $oldSegments = []): array {
        if ($lastSeq < 0) {
            return [];
        }
        $cuts = array_values(array_unique(array_map('intval', $cuts)));
        sort($cuts);
        $cuts = array_values(array_filter(
            $cuts,
            static fn($c) => $c >= 0 && $c < $lastSeq
        ));
        if ($cuts === []) {
            $title = '';
            if (count($oldSegments) === 1) {
                $title = trim((string) ($oldSegments[0]['title'] ?? ''));
            }
            return [[
                'title' => $title,
                'from_seq' => 0,
                'to_seq' => $lastSeq,
            ]];
        }
        $bounds = array_merge([-1], $cuts, [$lastSeq]);
        $segs = [];
        for ($i = 0; $i < count($bounds) - 1; $i++) {
            $from = $bounds[$i] + 1;
            $to = $bounds[$i + 1];
            $title = logimport_inherit_segment_title($from, $to, $oldSegments);
            $segs[] = [
                'title' => $title,
                'from_seq' => $from,
                'to_seq' => $to,
            ];
        }
        return $segs;
    }

    /**
     * Prefer old segment that shares the same from_seq (head of a cut keeps name).
     * Else exact range match. Else empty (do not invent "part N" here).
     */
    function logimport_inherit_segment_title(int $from, int $to, array $oldSegments): string {
        if ($oldSegments === []) {
            return '';
        }
        foreach ($oldSegments as $old) {
            if ((int) $old['from_seq'] === $from && (int) $old['to_seq'] === $to) {
                return trim((string) ($old['title'] ?? ''));
            }
        }
        foreach ($oldSegments as $old) {
            if ((int) $old['from_seq'] === $from
                && $to <= (int) $old['to_seq']
                && $from >= (int) $old['from_seq']
            ) {
                // head piece of a previously named segment
                return trim((string) ($old['title'] ?? ''));
            }
        }
        return '';
    }

    function logimport_segments_add_cut(array $segments, int $afterSeq, int $lastSeq): array {
        if ($afterSeq < 0 || $afterSeq >= $lastSeq) {
            return logimport_segments_normalize($segments, $lastSeq);
        }
        $base = $segments !== []
            ? $segments
            : [['from_seq' => 0, 'to_seq' => $lastSeq, 'title' => '']];
        $cuts = logimport_segment_cuts($base);
        if (!in_array($afterSeq, $cuts, true)) {
            $cuts[] = $afterSeq;
        }
        return logimport_segments_from_cuts($cuts, $lastSeq, $base);
    }

    function logimport_segments_remove_cut(array $segments, int $afterSeq, int $lastSeq): array {
        $cuts = logimport_segment_cuts($segments);
        $cuts = array_values(array_filter($cuts, static fn($c) => (int) $c !== $afterSeq));
        if ($cuts === []) {
            return [];
        }
        return logimport_segments_from_cuts($cuts, $lastSeq, $segments);
    }

    /**
     * @return array<int,int> seq => segmentIndex
     */
    function logimport_seq_to_segment(array $segments, int $lastSeq): array {
        if ($segments === []) {
            $map = [];
            for ($i = 0; $i <= $lastSeq; $i++) {
                $map[$i] = 0;
            }
            return $map;
        }
        $map = [];
        foreach ($segments as $si => $seg) {
            $from = (int) $seg['from_seq'];
            $to = (int) $seg['to_seq'];
            for ($i = $from; $i <= $to; $i++) {
                $map[$i] = $si;
            }
        }
        return $map;
    }
}
