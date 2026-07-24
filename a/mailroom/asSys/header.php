<?php
/** Brand bar only — left house mark, right session. Not a nav pane. */
$mrLogin = function_exists('mypi_room_href')
    ? mypi_room_href('base', 'login')
    : '/terminal/base/login';
$mrAuthed = function_exists('mypi_auth_user') && mypi_auth_user();
?>
<header class="mr-header nn_sightHeader" role="banner">
  <div class="mr-brand nn_naviLeft">
    <span class="mr-logo nn_HeaderLogo">CC</span>
    <span class="mr-title-plain">charlieWORK</span>
  </div>
  <nav class="mr-session nn_naviRight" aria-label="session">
    <?php if ($mrAuthed): ?>
      <form method="post" action="" class="mr-logout-form">
        <input type="hidden" name="authgate_action" value="logout">
        <button type="submit" class="mr-session-a">Logout</button>
      </form>
    <?php else: ?>
      <a class="mr-session-a" href="<?= htmlspecialchars($mrLogin, ENT_QUOTES, 'UTF-8') ?>">Login</a>
    <?php endif; ?>
    <?php /* O/X = skin set (night red / day blue). Not doors/home. */ ?>
    <button type="button" class="mr-session-a mr-skin-btn" id="mr-skin-o"
            data-theme="dark" title="night · red thread" aria-pressed="true">O</button>
    <button type="button" class="mr-session-a mr-skin-btn" id="mr-skin-x"
            data-theme="light" title="day · blue frost" aria-pressed="false">X</button>
  </nav>
</header>
