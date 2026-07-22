<?php
/**
 * Shared bits for WWW Tool Lab (pretend site inside explorer chrome).
 * Sky pages include this for dress + in-page nav.
 */
if (is_file(dirname(__DIR__, 3) . '/a/_/href_local.php')) {
    require_once dirname(__DIR__, 3) . '/a/_/href_local.php';
} elseif (is_file(echoSONAR . 'a/_/href_local.php')) {
    require_once echoSONAR . 'a/_/href_local.php';
}

function lab_href(string $key): string {
    if (function_exists('mypi_room_href')) {
        return mypi_room_href('lab', $key, 'www');
    }
    return '/www/lab/' . rawurlencode($key);
}

/** Head CSS for the lab site (does not touch master www asSys style). */
function lab_dress(): void {
    // Extra sheet into head via dressing bin (same rail as getA_Style)
    $GLOBALS['GETS']['dressing'][] = function () {
        if (function_exists('getA_Style')) {
            getA_Style('lab.style', 'www', 'asSys');
        }
    };

    quickDressing('wwwExplorer_innerShell', '
      background: #e8f0e8 !important;
      color: #1a2a1a;
      font-size: 1.05rem;
      overflow: auto !important;
    ');
    quickDressing('lab-site', 'padding: 0.5rem;');
}

/**
 * Banner + side nav + open main. Call lab_close() after page body tools.
 */
function lab_open(string $pageTitle, string $activeKey = 'home'): void {
    lab_dress();

    $links = [
        'home' => 'Lab home',
        'postbasic' => 'postBASIC posts',
        'cubook' => 'cuBOOK guestbook',
        'sopr' => 'soprBASIC fragments',
        'chat' => 'chatBOX room',
        'ledger' => 'ledgerREPORT',
        'toys' => 'Toy ROMs stage',
        'secret' => 'secretROOM (visual)',
    ];

    section('', 'lab-site');
    section('', 'lab-banner');
    h1('Tool Lab · pretend website');
    leaf('<span class="lab-tag">Inside WWW shell · tests for root t/tools · not the real internet</span>');
    close_section();

    section('', 'lab-layout');
    section('', 'lab-nav');
    leaf('<div class="lab-nav-h">Rooms</div>');
    foreach ($links as $key => $label) {
        $href = lab_href($key);
        $mark = ($key === $activeKey) ? ' <strong>←</strong>' : '';
        leaf('<a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">'
            . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</a>' . $mark);
    }
    leaf('<div class="lab-nav-h">Elsewhere</div>');
    leaf('<a href="/www/danyi/index">danyi.com</a>');
    leaf('<a href="/www/roms/games">roms / games</a>');
    close_section();

    section('', 'lab-main');
    medHeading($pageTitle);
}

function lab_close(): void {
    close_section(); // lab-main
    close_section(); // lab-layout
    section('', 'lab-foot');
    leaf('Tool Lab · hosted on SYS www · path /www/lab/… · styles: a/www/asSys/lab.style.css');
    close_section();
    close_section(); // lab-site
}
