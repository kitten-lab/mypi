<?php
$nav = $GLOBALS[BLOCK_ID]['NAV'];
$config = $nav['navSec'] ?? []; 
?>

<aside>
<nav class="bookNav">
<?php foreach ($nav as $ROOMS): ?>

<?php 
echo "<a href='/" . $ROOMS['DOM'] . '/' . $ROOMS['KEY'] . "'>" . $ROOMS['BUILDING'] . "</a> ||| ";

foreach ($ROOMS['ROOMS'] as $ROOM) {
  echo "<a href='/" . $ROOMS['DOM'] . '/' . $ROOM['KEY'] . "'>";
  echo $ROOM['ROOM'] . "</a> | ";
 }

endforeach; ?>
</div>

</nav></aside>
