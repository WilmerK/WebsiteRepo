<?php
session_start();
require 'dbStuff.php';

if (!isset($_SESSION['user_id'])) {
  die("You must be logged in to vote.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['post_id'], $_POST['vote_change'])) {
    $user_id = $_SESSION['user_id'];
    $post_id = intval($_POST['post_id']);
    $vote_change = intval($_POST['vote_change']); // should be 1 or -1

    $pdo = getDatabase();

    // Check existing vote
    $stmt = $pdo->prepare("SELECT vote_value FROM votes WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$user_id, $post_id]);
    $existing = $stmt->fetchColumn();

    $pdo->beginTransaction();

    try {
      if ($existing === false) {
        // No vote yet → insert vote
        $stmt = $pdo->prepare("INSERT INTO votes (user_id, post_id, vote_value) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $post_id, $vote_change]);

        $stmt = $pdo->prepare("UPDATE posts SET vote = vote + ? WHERE id = ?");
        $stmt->execute([$vote_change, $post_id]);

      } elseif ($existing == $vote_change) {
        // Same vote → remove (neutral)
        $stmt = $pdo->prepare("DELETE FROM votes WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$user_id, $post_id]);

        $stmt = $pdo->prepare("UPDATE posts SET vote = vote - ? WHERE id = ?");
        $stmt->execute([$vote_change, $post_id]);

      } else {
        // Opposite vote → update
        $stmt = $pdo->prepare("UPDATE votes SET vote_value = ? WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$vote_change, $user_id, $post_id]);

        // Net change is +2 or -2
        $netChange = 2 * $vote_change;
        $stmt = $pdo->prepare("UPDATE posts SET vote = vote + ? WHERE id = ?");
        $stmt->execute([$netChange, $post_id]);
      }

      $pdo->commit();
    } catch (Exception $e) {
      $pdo->rollBack();
      die("Vote failed: " . $e->getMessage());
    }
  }
}

$referer = $_SERVER['HTTP_REFERER'] ?? 'user.php';
header("Location: $referer");
exit;
