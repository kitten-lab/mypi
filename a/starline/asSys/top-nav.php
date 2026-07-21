<?php
// Unified b-front hrefs: /{SYS}/{DOM}/{KEY}
if (is_file(dirname(__DIR__, 2) . '/_/href_local.php')) {
    require_once dirname(__DIR__, 2) . '/_/href_local.php';
}
$h = function ($dom, $key) {
    return function_exists('mypi_room_href') ? mypi_room_href($dom, $key) : ('/starline/' . $dom . '/' . $key);
};
?>
<div class="navigation">
    <div class="nav-item"><a href="<?= htmlspecialchars($h('news', 'headlines')) ?>"><strong>News</strong></a></div>
    <div class="nav-item"><a href="<?= htmlspecialchars($h('chester', 'crates')) ?>">Crates</a></div>
    <div class="nav-item"><a href="<?= htmlspecialchars($h('charlie', 'threads')) ?>">Charlie</a></div>
    <div class="nav-item"><a href="<?= htmlspecialchars($h('satora', 'shelves')) ?>">TPS</a></div>
    <div class="nav-item"><a href="<?= htmlspecialchars($h('offices', 'frontdesk')) ?>">Front Desk</a></div>
    <div class="nav-item"><a href="<?= htmlspecialchars($h('offices', 'directory')) ?>">Directory</a></div>
    <div class="nav-item"><a href="<?= htmlspecialchars($h('offices', 'meetingroom')) ?>">Meeting Room</a></div>
</div>
