<?php
$nav = $GLOBALS['nav'];
$config = $GLOBALS['nav']['navSec'] ?? []; 

$SITE = BLOCK_ID;
$SKY_AUTH = $GLOBALS[$SITE]; ?>
<aside class="nav">
<nav>

<DIV class="main_nav">


<ul>

<div class="other_sections">
<h3>Agent MOD Online</h3>
</div>
<div id="room_logo">
<span id="modslug" class="mod-slug"></span>
<span id="mod" class="mod-name"></span>
<span id="key" class="room-location"></span>
<span id="comp" class="company"></span>
</div>
<div class="other_sections">
<h3>THE ROOMS OF <?= $SKY_AUTH['DOM_DISPLAY']; ?></h3></div>
</div>
<?php foreach ($nav as $section): ?>
<?php 


if ($section['DOM'] == $SKY_AUTH['DOM_SLUG']) {
 foreach ($section['ROOMS'] as $item) {
echo "<li id='" . $item['KEY'] . "'><a href='" . $item['KEY'] . "'>";
echo $item['ROOM'] . "</a></li>";
 }
}
endforeach; ?>



</DIV>
</ul>

</nav></aside>
<script>
  const SYS = <?php echo json_encode($SKY_AUTH['SYS']); ?>;
  const sys = <?php echo json_encode(strtolower($SKY_AUTH['SYS'])); ?>;
  const domVAR = <?php echo json_encode(strtolower($SKY_AUTH['DOM_SLUG'])); ?>;
  const roomVAR = <?php echo json_encode(strtolower($SKY_AUTH['ROOM_SLUG'])); ?>;
  const ROOM = <?php echo json_encode($SKY_AUTH['ROOM_DISPLAY']); ?>;
  const MOD = <?php echo json_encode(strtolower($SKY_AUTH['MOD_DISPLAY'])); ?>;
  const MODSLUG = <?php echo json_encode(strtolower($SKY_AUTH['MOD_SLUG'])); ?>;
  const DOM = <?php echo json_encode($SKY_AUTH['DOM_DISPLAY']); ?>;
  const checkKey = <?php echo json_encode($SKY_AUTH['ROOM_SLUG']); ?>;

  document.getElementById("modslug").innerHTML = MODSLUG;
  document.getElementById("mod").innerHTML = MOD;
  document.getElementById("key").innerHTML = "<BR>" + ROOM;
  document.getElementById("comp").innerHTML = SYS + " " + DOM;
  document.getElementById("room_logo").classList.add(sys + "-" + domVAR + "-" + roomVAR);
  document.getElementById(checkKey).classList.add("active");
</script>