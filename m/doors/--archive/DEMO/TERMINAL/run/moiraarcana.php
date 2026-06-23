<?php
SKY__AUTH(
    /*MOD_SLUG*/     "DEMO-SDK",
    /*MOD_DISPLAY*/  "sdk-808", 
    
    /*DOM_SLUG*/     "run", 
    /*DOM_DISPLAY*/  "terminal.root",

    /*ROOM_SLUG*/    "moiraarcana", 
    /*ROOM_DISPLAY*/  "Moira Arcana",

    /*ROOM_FLAVOR*/  "terminal-root"
);
openSky("limited access");
quickDressing("GameScreen","
  height: 40vh;
");
quickDressing("GameScreen h1::after", "
  content: '';
");
quickDressing("gameBtn", "
  font-size: 1rem;
  width: 24rem;
  border: 1px solid white;
");

getTool("postBASIC","StorePost.DEMO");

closeSky();
?>