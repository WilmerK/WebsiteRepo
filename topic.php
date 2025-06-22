<?php
require_once("sessionStuff.php");
require_once("dbStuff.php");

$topicName = $_GET['topic'] ?? null;
if (!$topicName) {
  die("No topic selected.");
}
$topic = $topicName;

// Get topic ID
$stmt = $db->prepare("SELECT id FROM topics WHERE name = ?");
$stmt->bind_param("s", $topicName);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0)
  die("Topic not found.");
$topicData = $result->fetch_assoc();
$topicId = $topicData['id'];

$pageTitle = "Topic: $topicName";


include("header.php");
?>

<body>
  <?php include("topbar.php"); ?>
  <?php include("sidebar.php"); ?>

  <main>
    <div class="content-container">
      <!-- ------------------ TOPIC HEADER ------------------ -->
      <?php include("profileHeader.php"); ?>
      <?php
      $currentUser = $_SESSION['user_id'];
      $followingTopic = false;

      // Check if following
      $stmt = $db->prepare("SELECT * FROM topicfollows WHERE follower_id = ? AND topic_id = ?");
      $stmt->bind_param("ii", $currentUser, $topicId);
      $stmt->execute();
      $res = $stmt->get_result();
      $followingTopic = $res->num_rows > 0;
      ?>

      <form action="follow.php" method="POST">
        <input type="hidden" name="topic_id" value="<?php echo $topicId; ?>">
        <button type="submit" class="create-post-btn"
          style="<?php echo $followingTopic ? 'background-color: #888;' : 'background-color: #FF4500;' ?>">
          <?php echo $followingTopic ? 'Unfollow Topic' : 'Follow Topic'; ?>
        </button>
      </form>


      <div class="center-column">
        <?php
        // Fetch posts tagged with this topic
        $query = "
        SELECT p.*, u.username, u.profile_picture
        FROM topicmessages tm
        JOIN posts p ON tm.post_id = p.id
        JOIN users u ON p.user_id = u.id
        WHERE tm.topic_id = ?
        ORDER BY p.created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $topicId);
        $stmt->execute();
        $posts = $stmt->get_result();

        while ($post = $posts->fetch_assoc()):
          ?>
          <div class="post">
            <div class="post-meta">
              <img src="<?php echo htmlspecialchars($post['profile_picture']); ?>" class="profile-pic-header">
              <strong>
                <a href="user.php?username=<?php echo urlencode($post['username']); ?>">
                  <?php echo htmlspecialchars($post['username']); ?>
                </a>
              </strong>
              &middot; <?php echo date("Y-m-d H:i", strtotime($post['created_at'])); ?>
            </div>

            <?php if (!empty($post['title'])): ?>
              <h3><?php echo htmlspecialchars($post['title']); ?></h3>
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

            <div class="post-actions">
              <form action="vote.php" method="POST">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <input type="hidden" name="vote_change" value="1">
                <button class="vote-btn upvote"><span class="material-symbols-outlined">arrow_upward</span></button>
              </form>

              <span><?php echo $post['vote']; ?></span>

              <form action="vote.php" method="POST">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <input type="hidden" name="vote_change" value="-1">
                <button class="vote-btn downvote"><span class="material-symbols-outlined">arrow_downward</span></button>
              </form>

              <a href="thread.php?post_id=<?php echo $post['id']; ?>" title="Comment">
                <span class="material-symbols-outlined">chat_bubble</span>
              </a>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  </main>
</body>

</html>