<?php
require_once("sessionStuff.php");
require_once("dbStuff.php");

$topicName = $_GET['topic'] ?? null;
if (!$topicName) {
  die("No topic selected.");
}

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
$topic = $topicName;

include("header.php");
?>

<body>
  <?php include("topbar.php"); ?>
  <?php include("sidebar.php"); ?>

  <main>
    <div class="content-container">
      <!-- ------------------ TOPIC HEADER ------------------ -->
      <div class="profile-header">
        <div class="profile-left">
          <img src="https://cdn-icons-png.flaticon.com/512/565/565547.png" alt="Topic Icon" class="profile-pic" />
          <div class="profile-info">
            <h2><?php echo htmlspecialchars($topic); ?></h2>
            <p class="bio">Discussion tagged with <strong><?php echo htmlspecialchars($topic); ?></strong></p>
          </div>
        </div>
      </div>

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
                $withEmbeds = preg_replace(
                  '/(https?:\/\/[^\s]+?\.(png|jpe?g|gif|webp))/i',
                  '<br><img src="imageProxy.php?url=$1" alt="Embedded Image" style="max-width:100%; border-radius:6px; margin-top:10px;">',
                  $escaped
                );
                echo $withEmbeds;
                ?>
              </p>
            <?php endif; ?>


            <?php if (!empty($post['media_url'])): ?>
              <div class="post-media">
                <img src="<?php echo htmlspecialchars($post['media_url']); ?>" alt="Post media">
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