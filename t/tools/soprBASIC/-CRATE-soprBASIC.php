<?php
/**
 * soprBASIC → ledger (kind=soper).
 * topic = section heading; body = fragment; meta.section_slug for grouping.
 * JSON sopr.frags in -v3/soprBASIC-json only.
 */

function soprBASIC_ledger_store(): array {
    require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';
    $place = mypi_ledger_place_from_sky();

    $section = trim((string) ($_POST['soper_section'] ?? ''));
    $leaf = trim((string) ($_POST['soper_leaf'] ?? ''));
    if ($section === '' && $leaf === '') {
        return ['ok' => false, 'error' => 'empty fragment'];
    }
    if ($section === '') {
        $section = 'loose';
    }
    $sectionSlug = strtolower(preg_replace('/\s+/', '', $section));
    $agent = (string) ($_POST['agent'] ?? 'user');

    return mypi_ledger_create_post([
        'topic' => $section,
        'body' => $leaf,
        'agent' => $agent,
        'tags_raw' => (string) ($_POST['POST__TAGS'] ?? ''),
        'timezone' => (string) ($_POST['POST__TZ'] ?? ''),
        'event_unix' => $_POST['POST__EVENT_UNIX'] ?? null,
        'sys' => $place['sys'],
        'dom' => $place['dom'],
        'room' => $place['room'],
        'mod' => $place['mod'],
        'place_label' => $place['place_label'],
        'tool' => 'soprBASIC',
        'tool_version' => 6,
        'kind' => 'soper',
        'actor' => $place['mod'] !== '' ? $place['mod'] : $agent,
        'meta' => [
            'section' => $section,
            'section_slug' => $sectionSlug,
            'payload' => 'soper',
        ],
    ]);
}
