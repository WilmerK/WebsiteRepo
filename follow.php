<?php
  require_once('dbStuff.php');
  require_once('sessionStuff.php');

$follower = $_SESSION['user_id'];
$target = intval($_POST['target_id']);

//Changes the follower status
if (changeFollower($follower, $target))
  header("Location: " . $_SERVER['HTTP_REFERER']);
  exit;

echo("There is an error.... IDIOT");
?>
