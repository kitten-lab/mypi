<?php
$base = '/mythleak';
$room = defined('ROOM_SLUG') ? strtolower((string) ROOM_SLUG) : 'headlines';
?>
<aside class="ml-nav" aria-label="Mythleak nav">
  <nav>
    <p class="ml-nav-sec">THE JUICE LINE</p>
    <ul>
      <li class="<?= $room === 'headlines' || $room === 'article' ? 'is-on' : '' ?>">
        <a href="<?= $base ?>/news/headlines">Latest Posts</a>
      </li>
      <li class="<?= $room === 'write' ? 'is-on' : '' ?>">
        <a href="<?= $base ?>/news/write">File a leak</a>
      </li>
    </ul>
    <p class="ml-nav-whisper">white-hat reporters · black-hat world</p>
  </nav>
</aside>
