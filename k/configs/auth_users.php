<?php
/**
 * mypi auth roster — reusable across surfaces (terminal, secretROOM, …).
 * Starline can grow a management UI later; for now this is the source of truth.
 *
 * Fields:
 *   username   — login id (normalized lower for match)
 *   keyphrase  — cute ritual password (not bank-grade)
 *   display    — MOD_DISPLAY / face name
 *   dom        — home terminal DOM (io, rx, …); null = any / surface-local
 *   sys        — home SYS (terminal, www, …); null = any
 *   roles      — optional tags for later ACL
 */
return [
    // classic secretROOM / auth_check lineage
    [
        'username' => 'SDK-777',
        'keyphrase' => 'kitkat',
        'display' => 'SDK-777',
        'dom' => 'io',
        'sys' => 'terminal',
        'roles' => ['terminal', 'legacy'],
    ],
    [
        'username' => 'SDK-808',
        'keyphrase' => 'hackthegibsonlespaul',
        'display' => 'SDK808',
        'dom' => 'io',
        'sys' => 'terminal',
        'roles' => ['terminal', 'io'],
    ],
    [
        'username' => 'sdk808',
        'keyphrase' => 'hackthegibsonlespaul',
        'display' => 'SDK808',
        'dom' => 'io',
        'sys' => 'terminal',
        'roles' => ['terminal', 'io'],
    ],
    [
        'username' => 'OLB369',
        'keyphrase' => 'lightbearer',
        'display' => "ORI'EL",
        'dom' => 'io',
        'sys' => 'terminal',
        'roles' => ['terminal', 'io'],
    ],
    [
        'username' => 'CH222',
        'keyphrase' => 'chestersimports',
        'display' => 'CHESTER',
        'dom' => 'io',
        'sys' => 'terminal',
        'roles' => ['terminal', 'io'],
    ],
    [
        'username' => 'KIT303',
        'keyphrase' => 'kitkat',
        'display' => 'KIT303',
        'dom' => 'io',
        'sys' => 'terminal',
        'roles' => ['terminal', 'io'],
    ],
    // Terminal AB — red station · Agent K (underground)
    [
        'username' => 'kde555',
        'keyphrase' => 'wireblood',
        'display' => 'Agent K',
        'dom' => 'ab',
        'sys' => 'terminal',
        'roles' => ['terminal', 'ab', 'agent-k'],
    ],
    [
        'username' => 'KDE555',
        'keyphrase' => 'wireblood',
        'display' => 'Agent K',
        'dom' => 'ab',
        'sys' => 'terminal',
        'roles' => ['terminal', 'ab', 'agent-k'],
    ],
    // Terminal ICU — Watchers · Teehee
    [
        'username' => 'the000',
        'keyphrase' => 'alwaysknew',
        'display' => 'Teehee',
        'dom' => 'icu',
        'sys' => 'terminal',
        'roles' => ['terminal', 'icu', 'watchers', 'teehee'],
    ],
    [
        'username' => 'THE-000',
        'keyphrase' => 'alwaysknew',
        'display' => 'Teehee',
        'dom' => 'icu',
        'sys' => 'terminal',
        'roles' => ['terminal', 'icu', 'watchers', 'teehee'],
    ],
    [
        'username' => 'THE000',
        'keyphrase' => 'alwaysknew',
        'display' => 'Teehee',
        'dom' => 'icu',
        'sys' => 'terminal',
        'roles' => ['terminal', 'icu', 'watchers', 'teehee'],
    ],
];
