<?php 

SKY__AUTH(
/*mod*/       "rheaporter",  "Rhea Porter",
/*dom*/       "offices", "Moon Station Offices",
/*room*/      "meetingroom", "The Meeting Room",
/* texture */ "classic"
);

openSky(ROOM_DISPLAY);
bigHeading(ROOM_DISPLAY);
getTool("chatBOX", "ChatBox");
bigHeading("Meeting Minutes");
leaf("This is a space for collaborative discussion and note-taking. Please use the chat box above to communicate with other participants and record important points from the meeting.");
getTool("chatBOX", "ChatRoom");
closeSky();
