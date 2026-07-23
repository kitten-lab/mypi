<?php
/**
 * authSession puppy — reusable PHP session auth (install on any surface).
 *
 * "Puppy" = PHP strut (sounds like pup, fetches identity).
 * Pair with t/tools/authGATE for the form face.
 *
 * Usage:
 *   require_once echoSONAR . 'k/puppies/authSession.puppy.php';
 *   mypi_auth_boot();
 *   mypi_auth_require(['sys' => 'terminal']);           // any terminal login
 *   mypi_auth_require(['sys' => 'terminal', 'dom' => 'io']);
 *   $agent = mypi_auth_agent(); // ['slug','display','dom','sys',…]
 */

if (!function_exists('mypi_auth_boot')) {

    function mypi_auth_boot(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // path-scoped enough for local mypi; surfaces share the pi
            session_name('mypi_auth');
            session_start([
                'cookie_httponly' => true,
                'cookie_samesite' => 'Lax',
            ]);
        }
    }

    function mypi_auth_users(): array
    {
        static $users = null;
        if ($users !== null) {
            return $users;
        }
        $path = (defined('echoSONAR') ? echoSONAR : '') . 'k/configs/auth_users.php';
        if (!is_file($path)) {
            // fallback legacy auth_check
            $legacy = (defined('echoSONAR') ? echoSONAR : '') . 'k/configs/auth_check.php';
            if (is_file($legacy)) {
                include $legacy;
                $list = $auth_check['logins'] ?? [];
                $users = [];
                foreach ($list as $row) {
                    $users[] = [
                        'username' => (string) ($row['username'] ?? ''),
                        'keyphrase' => (string) ($row['keyphrase'] ?? ''),
                        'display' => (string) ($row['username'] ?? ''),
                        'dom' => 'io',
                        'sys' => 'terminal',
                        'roles' => ['legacy'],
                    ];
                }
                return $users;
            }
            $users = [];
            return $users;
        }
        $users = include $path;
        if (!is_array($users)) {
            $users = [];
        }
        return $users;
    }

    function mypi_auth_normalize_user(string $u): string
    {
        return strtolower(trim($u));
    }

    /**
     * @return array<string,mixed>|null
     */
    function mypi_auth_find_user(string $username, string $keyphrase): ?array
    {
        $u = mypi_auth_normalize_user($username);
        $p = (string) $keyphrase;
        foreach (mypi_auth_users() as $row) {
            $ru = mypi_auth_normalize_user((string) ($row['username'] ?? ''));
            $rp = (string) ($row['keyphrase'] ?? '');
            if ($ru !== '' && $ru === $u && hash_equals($rp, $p)) {
                return $row;
            }
        }
        return null;
    }

    /**
     * @return array<string,mixed>|null session user payload
     */
    function mypi_auth_user(): ?array
    {
        mypi_auth_boot();
        $u = $_SESSION['mypi_auth'] ?? null;
        return is_array($u) ? $u : null;
    }

    function mypi_auth_check(): bool
    {
        return mypi_auth_user() !== null;
    }

    /**
     * Agent for SKY__AUTH / ledger — from session or fallback.
     *
     * @param array{slug?:string,display?:string} $fallback
     * @return array{slug:string,display:string,dom:string,sys:string}
     */
    function mypi_auth_agent(array $fallback = []): array
    {
        $u = mypi_auth_user();
        if ($u) {
            return [
                'slug' => (string) ($u['username'] ?? $u['slug'] ?? 'user'),
                'display' => (string) ($u['display'] ?? $u['username'] ?? 'user'),
                'dom' => (string) ($u['dom'] ?? ''),
                'sys' => (string) ($u['sys'] ?? ''),
            ];
        }
        return [
            'slug' => (string) ($fallback['slug'] ?? 'guest'),
            'display' => (string) ($fallback['display'] ?? 'GUEST'),
            'dom' => '',
            'sys' => '',
        ];
    }

    /**
     * @param array{sys?:string,dom?:string,redirect?:string} $need
     */
    function mypi_auth_require(array $need = []): void
    {
        mypi_auth_boot();
        $u = mypi_auth_user();
        $loginPath = $need['redirect'] ?? mypi_auth_login_url($need);

        if (!$u) {
            mypi_auth_redirect($loginPath);
        }

        $needSys = isset($need['sys']) ? strtolower((string) $need['sys']) : '';
        $needDom = isset($need['dom']) ? strtolower((string) $need['dom']) : '';
        $haveSys = strtolower((string) ($u['sys'] ?? ''));
        $haveDom = strtolower((string) ($u['dom'] ?? ''));

        if ($needSys !== '' && $haveSys !== '' && $haveSys !== $needSys) {
            mypi_auth_redirect($loginPath);
        }
        // terminal: dom is assigned by login — must match station
        if ($needDom !== '' && $haveDom !== '' && $haveDom !== $needDom) {
            mypi_auth_redirect($loginPath);
        }
    }

    function mypi_auth_login_url(array $need = []): string
    {
        if (function_exists('mypi_room_href') && (($need['sys'] ?? '') === 'terminal' || ($need['sys'] ?? '') === '')) {
            return mypi_room_href('base', 'login');
        }
        $sys = (string) ($need['sys'] ?? (defined('SYS_ID') ? SYS_ID : 'terminal'));
        return '/' . rawurlencode($sys) . '/base/login';
    }

    function mypi_auth_redirect(string $path): void
    {
        if ($path === '') {
            $path = '/terminal/base/login';
        }
        if ($path[0] !== '/' && !preg_match('#^https?://#i', $path)) {
            $path = '/' . $path;
        }
        // preserve next
        $here = $_SERVER['REQUEST_URI'] ?? '';
        if ($here && strpos($path, 'next=') === false && strpos($here, '/base/login') === false) {
            $sep = strpos($path, '?') !== false ? '&' : '?';
            $path .= $sep . 'next=' . rawurlencode($here);
        }
        header('Location: ' . $path);
        exit;
    }

    /**
     * Establish session from roster row.
     *
     * @param array<string,mixed> $row
     */
    function mypi_auth_login_row(array $row): void
    {
        mypi_auth_boot();
        $slug = (string) ($row['username'] ?? '');
        $_SESSION['mypi_auth'] = [
            'username' => $slug,
            'slug' => $slug,
            'display' => (string) ($row['display'] ?? $slug),
            'dom' => (string) ($row['dom'] ?? ''),
            'sys' => (string) ($row['sys'] ?? 'terminal'),
            'roles' => $row['roles'] ?? [],
            'login_unix' => time(),
        ];
    }

    function mypi_auth_logout(): void
    {
        mypi_auth_boot();
        unset($_SESSION['mypi_auth']);
    }

    /**
     * Attempt login; returns user row or null.
     */
    function mypi_auth_attempt(string $username, string $keyphrase): ?array
    {
        $row = mypi_auth_find_user($username, $keyphrase);
        if ($row) {
            mypi_auth_login_row($row);
        }
        return $row;
    }

    /** Home path after login (assigned terminal). */
    function mypi_auth_home_path(?array $user = null): string
    {
        $u = $user ?? mypi_auth_user();
        if (!$u) {
            return mypi_auth_login_url(['sys' => 'terminal']);
        }
        $dom = (string) ($u['dom'] ?? 'io');
        $sys = (string) ($u['sys'] ?? 'terminal');
        if ($dom === '') {
            $dom = 'io';
        }
        // per-station home room (RX = VEN medicine cabinet)
        $homeRoom = 'files';
        $domL = strtolower($dom);
        if ($domL === 'rx') {
            $homeRoom = 'ven';
        }
        if (function_exists('mypi_room_href') && $sys === 'terminal') {
            return mypi_room_href($dom, $homeRoom);
        }
        return '/' . rawurlencode($sys) . '/' . rawurlencode($dom) . '/' . rawurlencode($homeRoom);
    }
}
