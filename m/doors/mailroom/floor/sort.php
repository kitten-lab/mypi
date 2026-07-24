<?php
/**
 * Master Mailroom · sort floor — timber index + Charlie tags
 */
SKY__AUTH(
    'charlie',
    'Charlie',
    'floor',
    'Floor',
    'sort',
    'Sort',
    'classic'
);

openSky('MAILROOM · SORT');

// Facility shell owns chrome; tool fills slots
getTool('timberBay', 'Rail');  // → sidebar controls only
getTool('timberBay', 'Desk');  // → yard rows + manage panel

closeSky();
