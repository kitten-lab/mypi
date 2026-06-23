<?php 

SKY__AUTH(
    /*MOD_SLUG*/     "book", 
    /*MOD_DISPLAY*/  "the words bleed through", 
    
    /*DOM_SLUG*/     "tavern", 
    /*DOM_DISPLAY*/  "tavern",

    /*ROOM_SLUG*/    "dwglens", 
    /*ROOM_DISPLAY*/  "Dark Ward's Glen Tavern",

    /*ROOM_FLAVOR*/  
);
openSky('A Chat Room');

getTool("chatBOX","ChatBox"); 
hr();   
getTool("chatBOX","ChatRoom");    

closeSky();

 ?>