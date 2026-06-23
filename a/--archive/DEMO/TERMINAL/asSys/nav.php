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
echo "<a href='" . b_root . '/' . $SKY_AUTH['URI'] . '?' . $section['DOM'] . '=' . $section['PRIME_KEY'] . "'>" . $section['BUILDING'] . "</a>";

 foreach ($section['ROOMS'] as $item) {
echo "<li><a href='" . b_root . '/' . $SKY_AUTH['URI'] . '?' . $section['DOM'] . '=' . $item['ROOM'] . "'>";
echo $item['KEY'] . "</a></li>";
 }

endforeach; ?>
</div>


</DIV>
</ul>

</nav></aside>
