<?php 

SKY__AUTH(
    /*MOD_SLUG*/     "www", 
    /*MOD_DISPLAY*/  "Mira Mobile Media", 
    
    /*DOM_SLUG*/     "terminal_girls", 
    /*DOM_DISPLAY*/  "programs",

    /*ROOM_SLUG*/    "oriel-chat", 
    /*ROOM_DISPLAY*/  "The Quiet River",

    /*ROOM_FLAVOR*/  
);
openSky('A Chat Room');

getTool("chatBOX","ChatBox"); 
hr();   
getTool("chatBOX","ChatRoom");    

closeSky();

 ?>