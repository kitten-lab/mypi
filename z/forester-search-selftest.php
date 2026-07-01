<?php
/**
 * CLI self-test for forester search helpers.
 *
 * Usage:
 *   C:\xampp\php\php.exe z\forester-search-selftest.php
 */

declare(strict_types=1);

require_once __DIR__ . '/forester-search-lib.php';

$ok = foresterSearchSelfTest();
exit($ok ? 0 : 1);