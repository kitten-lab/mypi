<?php
SKY__AUTH(
    $site,
    $sys,
    $site,
    "ROOT", // storage slug of #MOD
    "ROOT", // display name of MOD
    "communications", // building slug #DOM
    "COMMS", // building display name
    "plog-list", // room slug #ROOM
    "LIST.TXT",// room display name
    "skyline-standard"
);
openSky($sys . '.' . $dom);

section($right,'color:gray','');
bigHeading("TERMINAL.ROOT COMMUNITS");
leaf("the public considerations of the terminal at its root.
$BR  .........");
close_section();
getTool('postBASIC', 'NewView');


closeSky();
?>