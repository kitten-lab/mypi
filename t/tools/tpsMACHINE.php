<?php

$event_time = (int)filter_var($_POST['POST__EVENT_UNIX'], FILTER_SANITIZE_NUMBER_INT);

        $unix = time();
        $tzone = $_POST['POST__TZ'];
        $ms = round(microtime(true) * 1000);
        $time = new DateTime("@$unix");
        $time->setTimezone(new DateTimeZone($tzone));
        $timezone = $time->format("e");
        $localtime = $time->format("h:i:sA");

        if (!$event_time == '') {
            $tpstime = $event_time;
        } else {
            $tpstime = $unix;
            $event_time = $unix;
        }
    
        $event_calc = new DateTime("@$event_time");
        $sdate = $event_calc->format('Y-m-d');
        $syear = $event_calc->format('Y');

        