<?php
/**
 * SYS starline · DOM satora · ROOM shelves
 * First-class TPS report (mirrors d/_SATORA sense).
 */
SKY__AUTH(
    'system', 'System Voice',
    'satora', 'Satora — TPS',
    'shelves', 'TPS time windows',
    'classic'
);
openSky(ROOM_DISPLAY);
getTool('ledgerREPORT', 'Tps');
closeSky();
