<?php
$nav = $GLOBALS['nav'];
$config = $GLOBALS['nav']['navSec'] ?? []; ?>

<h1 class="pageTitle flicker">
<?= $GLOBALS['mod'] ?></h1>
<h3 style="padding-bottom:0px;">
[<a href="<?= 'index.php?mod=' . $mod . '&pv=' . $pv ?>"> Home </a>] 
[<a href="<?= 'index.php?mod=' . $mod . '&pv=' . $pv ?>"> Login </a>]
</h1>
<aside class="nav">

<nav>
<ul>


<?php foreach ($nav as $section): ?>
<BR>
<span class="navSec">
<?php echo $section['name']; ?></span>
<?php foreach ($section['items'] as $item): ?>

<li>
<a href="<?= '/b/' . $GLOBALS['SITE_SLUG'] . '/' . $item['door'] . '/' . $item['key'] ?>">


<?= $item['label']; ?></a>
</li>
<?php foreach ($item['subSec'] as $subItem): ?>
<li> 
<a href="<?= $subItem['path']  ?>" class="navSubSec">REFACTOR<?= $subItem['label']; ?></a>
</li>

<?php endforeach; ?>
<?php endforeach; ?>
<?php endforeach; ?>
</ul>
<div style="height: 100px; overflow:hidden; text-align:center; opacity:0.3;">
<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
  <path 
    d="
      M50 10
      L50 45
      C50 65, 30 65, 30 50
      C30 35, 50 35, 60 45
      L75 35
    "
    fill="none"
    stroke="white"
    stroke-width="3"
    stroke-linecap="round"
    stroke-linejoin="round"
  />
</svg></div>
</nav></aside>