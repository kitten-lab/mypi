<?php
$nav = $GLOBALS['nav'];
$config = $GLOBALS['nav']['navSec'] ?? []; 
$SKY_AUTH = $GLOBALS[$SITE]; ?>
<aside class="nav">
<nav>
<DIV class="main_nav"><ul>
<div class="ROOM_ID">

<?php foreach ($nav as $section): ?>
<?php 
echo $section['BUILDING'];

 foreach ($section['ROOMS'] as $item) {
echo "<li><a href='" . b_root . '/' . $SKY_AUTH['URI'] . '/' . $section['DOOR'] . '/' . $item['KEY'] . "'>";
echo $item['ROOM'] . "</a></li>";
 }

endforeach; ?>
</div>


</DIV>
</ul>

</nav></aside>
