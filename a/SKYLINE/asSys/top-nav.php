<?php
$topnav = $GLOBALS['nav'];
$config = $GLOBALS['nav']['navSec'] ?? []; 
$SKY_AUTH = $GLOBALS[$SITE]; ?>


<div class="topNav">


<?php 


echo "<span>buildings: </span>";
foreach ($topnav as $section) {
echo "<span id='" . $section['DOM'] . "'>"; 
echo "<a href='/" . $section['DOM'] . '/' . $section['KEY'] . "'>";
echo $section['BUILDING'] . "</a></span>";
}
 ?>
 
</div>
<script>
const dom_display = <?php echo json_encode($SKY_AUTH['DOM_SLUG']); ?>;
const section_dom = <?php echo json_encode($section['DOM']); ?>;

document.getElementById(dom_display).classList.add("active");
</script>