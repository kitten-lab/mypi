<?php
/**
 * CLI self-test for forester quote helpers.
 *
 * Usage:
 *   C:\xampp\php\php.exe t\tools\foresterQUOTE\forester-quote-selftest.php
 */

declare(strict_types=1);

require_once __DIR__ . '/forester-quote-lib.php';

$ok = foresterQuoteSelfTest();
exit($ok ? 0 : 1);
