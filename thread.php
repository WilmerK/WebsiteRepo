<?php
// ==========================================================
// POST VIEW PAGE (Single Post + Comments)
// ==========================================================

require_once('dbStuff.php');
require_once('sessionStuff.php');
require_once('fancy.php');

$pdo = getDatabase();

$post_id = null;

// Handle GET or POST source of post_id
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
  $post_id = intval($_POST['post_id']);
} elseif (isset($_GET['post_id'])) {
  $post_id = intval($_GET['post_id']);
} else {
  header("Location: home.php");
  exit();
}

// Fetch post using $post_id
$stmt = $pdo->prepare("SELECT posts.*, users.username, users.full_name FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
  echo "Post not found.";
  exit();
}


// Fetch comments
$commentStmt = $pdo->prepare("SELECT postreplies.*, users.username FROM postreplies JOIN users ON postreplies.user_id = users.id WHERE post_id = ? ORDER BY created_at ASC");
$commentStmt->execute([$post_id]);
$comments = $commentStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Post View</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
  <link rel="stylesheet" href="user.css">
</head>

<body>
  <!-- Header -->
  <header>
    <div class="top-left">
      <img class="logo" src="images/4chenlogo.jpg" alt="4chen Logo" />
      <span class="brand">4chen</span>
    </div>
    <div class="search-container">
      <input type="text" class="search-bar" placeholder="Search..." />
    </div>
    <a class="top-right" href="user.php?username=<?php echo urlencode($_SESSION['myUsername']); ?>">
      <img src="https://randomuser.me/api/portraits/men/68.jpg" class="profile-pic-header" />
    </a>
  </header>

  <!-- Sidebar -->
  <aside class="sidebar">
    <?php fancyLeft(); ?>
  </aside>

  <a href="user.php?username=<?php echo urlencode($post['username']); ?>" class="back-button" title="Back">
    <span class="material-symbols-outlined">arrow_back</span>
  </a>



  <!-- Main -->
  <main>
    <div class="content-container center-column">

      <!-- Display Single Post -->
      <div class="post">
        <div class="post-meta">
          <?php
          echo htmlspecialchars($post['username']) . ' – ' . date("Y-m-d H:i", strtotime($post['created_at']));
          ?>
        </div>
        <?php if (!empty($post['caption'])): ?>
          <p><?php echo nl2br(htmlspecialchars($post['caption'])); ?></p>
        <?php endif; ?>
        <?php if (!empty($post['media_url'])): ?>
          <div class="post-media">
            <img src="<?php echo htmlspecialchars($post['media_url']); ?>" alt="Post media">
          </div>
        <?php endif; ?>
      </div>

      <!-- Comments Section -->
      <h4 style="margin-top: 20px;">Comments</h4>
      <?php foreach ($comments as $comment): ?>
        <div class="post">
          <div class="post-meta">
            <?php echo htmlspecialchars($comment['username']) . ' – ' . date("Y-m-d H:i", strtotime($comment['created_at'])); ?>
          </div>
          <p><?php echo nl2br(htmlspecialchars($comment['reply_text'])); ?></p>
        </div>
      <?php endforeach; ?>

      <!-- Comment Form -->
      <form action="thread.php" method="POST" style="margin-top: 15px;">
        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
        <textarea name="reply_text" rows="3" style="width: 100%; padding: 10px;" placeholder="Add a comment..."
          required></textarea>
        <button type="submit" style="margin-top: 5px;" class="create-post-btn">Post Comment</button>
      </form>

      <?php
      // Handle comment submission
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_text'], $_POST['post_id'])) {
        $reply_text = trim($_POST['reply_text']);
        $uid = getUserIDFromUsername($_SESSION['myUsername']);
        if (!empty($reply_text)) {
          $insert = $pdo->prepare("INSERT INTO postreplies (post_id, user_id, reply_text, created_at) VALUES (?, ?, ?, NOW())");
          $insert->execute([$post_id, $uid, $reply_text]);
          header("Location: thread.php?post_id=$post_id");
          exit();
        }
      }
      ?>

    </div>
  </main>
</body>

</html>