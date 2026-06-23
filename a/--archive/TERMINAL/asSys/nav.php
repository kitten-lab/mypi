<?php
$nav = $GLOBALS['nav'];
$config = $GLOBALS['nav']['navSec'] ?? []; ?>

<h1 class="pageTitle flicker" style="font-size:1.6vh;">
<?= $GLOBALS[$SITE]['SYS_DISPLAY'] ?></h1>
logged in as: <?= $GLOBALS[$SITE]['MOD_DISPLAY'] ?><BR><br>
<aside class="nav">

<nav>
<?php foreach ($nav as $section): ?>

<ul>
<span class="navSec">
<?php echo $section['BUILDING']; ?></span>
<?php foreach ($section['ROOMS'] as $item): ?>

<li>
<a href="<?= '/' . SUBBLOCK_ID . '/' . $section['DOM'] . '/' . $item['KEY'] ?>">


<?= $item['ROOM']; ?></a>
</li>
<?php endforeach; ?>
</ul>
<?php endforeach; ?>
</nav></aside>