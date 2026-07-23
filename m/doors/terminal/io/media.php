<?php
/**
 * Serve house media from d/_MEDIA (auth-gated · local only).
 * GET ?id=m.HEX
 *
 * Clean buffers so no HTML/chrome corrupts binary output.
 */
require_once __DIR__ . '/../_tm_auth.php';
$agent = tm_require_station('io');

require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

// Drop any prior output (warnings, BOM, shell crumbs)
while (ob_get_level() > 0) {
    ob_end_clean();
}

$id = isset($_GET['id']) ? (string) $_GET['id'] : '';
$path = mypi_media_resolve($id);
if ($path === null || !is_file($path)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo "media not found\n";
    exit;
}

$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$types = [
    'png' => 'image/png',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    'svg' => 'image/svg+xml',
];
$mime = $types[$ext] ?? 'application/octet-stream';
$len = filesize($path);
header('Content-Type: ' . $mime);
header('Content-Length: ' . (string) $len);
header('Cache-Control: private, max-age=86400');
header('X-Content-Type-Options: nosniff');
readfile($path);
exit;
