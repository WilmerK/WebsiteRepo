<?php
// Session timeout is 10 minutes
$sessionTimeout = 600;

require_once('dbStuff.php');
session_start();

// Check if the session was set
if (!isset($_SESSION['sessionid'])) {
    // Redirect to login if no session
    header("Location: login.php");
    exit();
} else {
    $sessionid = $_SESSION['sessionid'];

    $sessionTime = checkSessionInDB($sessionid);
    
    //Check if the session even exists.
    if ($sessionTime === '') {
        // Session not found in DB
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }

    //Check if the session has expired.
    if (time() - $sessionTime > $sessionTimeout) {
        clearSession($sessionid);
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    } else {
        // Session is valid, update last active time
        updateSessionTime($sessionid);
    }
}
?>