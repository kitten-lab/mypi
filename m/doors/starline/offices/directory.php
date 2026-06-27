<?php 

SKY__AUTH(
/*mod*/       "rheaporter",  "Rhea Porter",
/*dom*/       "offices", "Moon Station Offices",
/*room*/      "directory", "The Great Directory",
/* texture */ "classic"
);

openSky(ROOM_DISPLAY);
bigHeading(ROOM_DISPLAY);
leaf("A selection of our top surfaces:
<a href='http://starline/'>Starline</a>
<a href='http://nim/'>Nim</a>
<a href='http://backrooms/'>Back Rooms</a>");

bigHeading("Event Directory");
leaf("A few inexplicable events:
<a href='/events/mirror-box-ritual'>Mirror Box Ritual</a>");
closeSky();
