
<script>

  const sessionStatus = <?php echo isset($_SESSION['skyAUTH']) ? 'true' : 'false'; ?>;

if (!sessionStorage.getItem('skyAUTH')){
        document.getElementById("browserWindow").innerHTML = "You're not logged in.";
        document.getElementById("browserWindow").innerHTML += "<button type='submit' onclick='LetsBEGIN()'>Login</button>";
} else {
    let browserWindow = document.getElementById("browserWindow");
    browserWindow.insertAdjacentHTML("beforebegin","<button type='submit' onclick='LetsEND()'>Logout</button>");
}

if (sessionStorage.getItem('skyAUTH')) {
    console.log("Session item exists");
} else {
    console.log("Session item not found");
}

function LetsBEGIN(){
sessionStorage.setItem("skyAUTH","SDK-808");
    sessionSET = <?php echo json_encode(["true" => isset($_SESSION['skyAUTH'])]); ?>;


    location.reload();

}


function LetsEND(){
sessionStorage.removeItem("skyAUTH");
    sessionSET = <?php echo json_encode(["false" => isset($_SESSION['skyAUTH'])]); ?>;
location.reload();
if (sessionStorage.getItem('skyAUTH')) {
    console.log("Session item exists");
} else {
    console.log("Session item not found");
}

}

</script>