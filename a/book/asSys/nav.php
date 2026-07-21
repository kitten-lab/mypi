<?php
/**
 * Book nav — local vhost DocumentRoot is b/book → /DOM/KEY only (no extra SYS).
 * Cosmology: SYS/DOM/ROOM/MOD. Helper: a/_/href_local.php
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
  $buildingKey = $ROOMS['KEY'] ?? '';
  if ($buildingKey === '' || $buildingKey === 'home' || $buildingKey === $dom) {
      $buildingKey = $ROOMS['ROOMS'][0]['KEY'] ?? $dom;
  }
  $buildingHref = function_exists('mypi_room_href')
    ? mypi_room_href($dom, $buildingKey)
    : ('/' . $dom . '/' . $buildingKey);
  echo "<a href='" . htmlspecialchars($buildingHref, ENT_QUOTES, 'UTF-8') . "'>"
     . htmlspecialchars($ROOMS['BUILDING'] ?? $dom, ENT_QUOTES, 'UTF-8')
     . "</a> ||| ";

  foreach ($ROOMS['ROOMS'] as $ROOM) {
      $href = function_exists('mypi_room_href')
        ? mypi_room_href($dom, $ROOM['KEY'] ?? '')
        : ('/' . $dom . '/' . ($ROOM['KEY'] ?? ''));
      echo "<a href='" . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . "'>";
      echo htmlspecialchars($ROOM['ROOM'] ?? $ROOM['KEY'] ?? '', ENT_QUOTES, 'UTF-8');
      echo "</a> | ";
  }
endforeach; ?>
</nav>
</aside>
