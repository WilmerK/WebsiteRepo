<?php
    //This could be a cool addision where we email the new user to confirm that this is not a bot.
    function mailToVerify($email)
    {
        $subject = "New APPNAME User";
        $message = "Please click this link to verify your email";
        $headers = "From: sender@example.com";
        mail($email, $subject, $message, $headers);
    }

function fancyHeader() {
    echo "<header style='background:#eee;padding:1em;text-align:center;'>
            <h1>My Reddit-Like Site</h1>
          </header>";
}

function fancyLeft() {
    echo "<div style='padding:1em;'>Sidebar Placeholder</div>";
}

function fancyRight() {
    echo "<div style='padding:1em;'>Right Side Placeholder</div>";
}
?>