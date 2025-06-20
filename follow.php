<?php
require_once('dbStuff.php');
require_once('sessionStuff.php');

$follower = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // FOLLOW USER
  if (isset($_POST['target_id'])) {
    $target = intval($_POST['target_id']);
    if (changeFollower($follower, $target)) {
      header("Location: " . $_SERVER['HTTP_REFERER']);
      exit();
    }
  }

  // FOLLOW TOPIC
  if (isset($_POST['topic_id'])) {
    $topic_id = intval($_POST['topic_id']);
    if (changeTopicFollow($follower, $topic_id)) {
      header("Location: " . $_SERVER['HTTP_REFERER']);
      exit();
    }
  }
}

echo("There was an error processing the follow request.");
?>
