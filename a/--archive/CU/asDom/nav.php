<?php
$nav = $GLOBALS['nav'];
$config = $GLOBALS['nav']['navSec'] ?? []; ?>

<aside class="nav">

<nav>
<h1 class="pageTitle flicker">
<?= $GLOBALS['mod'] ?></h1>
<h3 style="padding-bottom:0px;">
[<a href="<?= 'index.php?mod=' . $mod . '&pv=' . $pv ?>"> Home </a>] 
[<a href="<?= 'index.php?mod=' . $mod . '&pv=' . $pv ?>"> Login </a>]
</h1>
<ul>


<?php foreach ($nav as $section): ?>
<BR>
<span class="navSec">
<?php echo $section['name']; ?></span>
<?php foreach ($section['items'] as $item): ?>

<li>
<a href="<?= b_root . '/' . $site . '/' . $item['door'] . '/' . $item['key'] ?>">


<?= $item['label']; ?></a>
</li>
<?php endforeach; ?>
<?php endforeach; ?>
</ul>
</nav></aside>