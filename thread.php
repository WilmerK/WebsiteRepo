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
$stmt = $pdo->prepare("SELECT posts.*, users.username, users.full_name, users.profile_picture FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?");
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
include('header.php');
?>

<body>
  <!-- Header -->
  <?php include('topbar.php'); ?>
  <?php include('sidebar.php'); ?>

  <a href="user.php?username=<?php echo urlencode($post['username']); ?>" class="back-button" title="Back">
    <span class="material-symbols-outlined">arrow_back</span>
  </a>

  <!-- Main -->
  <main>
    <div class="content-container center-column">

      <!-- Display Single Post -->
      <div class="post">
        <div class="post-meta">
          <img src="<?php echo htmlspecialchars($post['profile_picture']); ?>" class="profile-pic-header">
          <strong>
            <a href="user.php?username=<?php echo urlencode($post['username']); ?>">
              <?php echo htmlspecialchars($post['username']); ?>
            </a>
          </strong>
          <?php echo date("Y-m-d H:i", strtotime($post['created_at'])); ?>
        </div>

        <?php if (!empty($post['title'])): ?>
          <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
        <?php endif; ?>

        <?php if (!empty($post['caption'])): ?>
          <p class="post-caption">
            <?php
            $caption = $post['caption'];
            $escaped = nl2br(htmlspecialchars($caption));
            // First: embed videos
            $withEmbeds = preg_replace(
                '/(https?:\/\/[^\s]+?\.(mp4|webm|ogg))/i',
                '<br><video controls style="max-width:100%; border-radius:6px; margin-top:10px;"><source src="imageProxy.php?url=$1" type="video/$2">Your browser does not support the video tag.</video>',
                $escaped
            );

            // Then: embed images
            $withEmbeds = preg_replace(
                '/(https?:\/\/[^\s]+?\.(png|jpe?g|gif|webp))/i',
                '<br><img src="imageProxy.php?url=$1" alt="Embedded Image" style="max-width:100%; border-radius:6px; margin-top:10px;">',
                $withEmbeds
            );
            echo $withEmbeds;
            ?>
          </p>
        <?php endif; ?>


        <?php if (!empty($post['media_url'])): ?>
          <div class="post-media">
            <?php
              //Get the media url
              $mediaUrl = htmlspecialchars($post['media_url'], ENT_QUOTES);

              // Get the file extension
              $extension = strtolower(pathinfo($mediaUrl, PATHINFO_EXTENSION));

              // Define supported types
              $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
              $videoTypes = ['mp4', 'webm', 'ogg'];

              //Check if the format is good
              if (in_array($extension, $imageTypes)) {
                  //If it is of type image, post the image
                  echo '<img src="' . $mediaUrl . '" alt="Post media" style="max-width:100%; border-radius:8px; margin-top:10px;">';
              } elseif (in_array($extension, $videoTypes)) {
                  //If it is of type video, post the video
                  echo '<video controls style="max-width:100%; border-radius:8px; margin-top:10px;">
                          <source src="' . $mediaUrl . '" type="video/' . $extension . '">
                          Your browser does not support the video tag.
                      </video>';
              } else {
                  echo '<p>Unsupported media format.</p>';
              }
            ?>
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