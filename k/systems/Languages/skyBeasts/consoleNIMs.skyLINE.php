<?php
// consoleNIMs are Nimble handles for console messages

function Console_Log($message, ?string $color = null){
    if ($color !== null){
  GetFILLER("<script>setTimeout(() => {console.log('%c" . $message . "','" . $color . "')  }, 500);</script>", "scripts");

    } else {
  GetFILLER("<script>setTimeout(() => {console.log('" . $message . "')  }, 1000);</script>", "scripts");

    }
}

function Console_Log_Warning($message){
  GetFILLER("<script>setTimeout(() => {console.warn('" . $message . "')  }, 1000);</script>", "scripts");
}


function Console_Log_Note($message){
  GetFILLER("<script>setTimeout(() => {console.log('%c" . $message . "', 'color:orange');  }, 1000);</script>", "scripts");
}

function KDE_Error_Logger($tool,$error){
    GetFILLER("<script>console.error(`KDE_ERROR_LOG: " . $tool . " " . $error . "`);</script>", "scripts");
}

