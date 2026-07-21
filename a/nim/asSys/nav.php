<?php
/**
 * Local SYS vhost: /DOM/KEY only (see a/_/href_local.php, docs/CLEANUP-HALF-MIGRATION.md).
 */
if (is_file(dirname(__DIR__, 2) . '/_/href_local.php')) {
    require_once dirname(__DIR__, 2) . '/_/href_local.php';
}
$nav = $GLOBALS[BLOCK_ID]['NAV'] ?? [];
$sections = [];
foreach ($nav as $section) {
    if (is_array($section) && !empty($section['DOM'])) {
        $sections[] = $section;
    }
}
?>
<aside>
<nav class="bookNav">
<?php foreach ($sections as $ROOMS): ?>
<?php
  $dom = $ROOMS['DOM'];
  $bKey = $ROOMS['KEY'] ?? ($ROOMS['ROOMS'][0]['KEY'] ?? $dom);
  $bh = function_exists('mypi_room_href') ? mypi_room_href($dom, $bKey) : ('/' . $dom . '/' . $bKey);
  echo "<a href='" . htmlspecialchars($bh, ENT_QUOTES, 'UTF-8') . "'>"
     . htmlspecialchars($ROOMS['BUILDING'] ?? $dom, ENT_QUOTES, 'UTF-8') . "</a> ||| ";
  foreach ($ROOMS['ROOMS'] as $ROOM) {
      $k = $ROOM['KEY'] ?? '';
      $h = function_exists('mypi_room_href') ? mypi_room_href($dom, $k) : ('/' . $dom . '/' . $k);
      echo "<a href='" . htmlspecialchars($h, ENT_QUOTES, 'UTF-8') . "'>"
         . htmlspecialchars($ROOM['ROOM'] ?? $k, ENT_QUOTES, 'UTF-8') . "</a> | ";
  }
endforeach; ?>
</nav>
</aside>
