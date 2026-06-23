<?php $config = $nav ?? []; ?>

<aside class="nav"><nav>
<ul>

<h1>HI <?= $mod ?></h1>

<?php foreach ($nav as $item): ?><li>
<a href="<?= $location . $item['path'] . '?mod=' . $mod; ?>"><?= $item['label']; ?></a>
</li><?php endforeach; ?>

</ul>
</nav></aside>
<span class="ioooooooooooooooooo45u8222"></span>