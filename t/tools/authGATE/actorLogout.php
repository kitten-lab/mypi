<?php
// Logout POSTs hit actorLogin (same authgate_action=logout) when Login tool is installed.
// Standalone Logout tool also loads this file name via getTool('authGATE','Logout') → actorLogout.
require __DIR__ . '/actorLogin.php';
