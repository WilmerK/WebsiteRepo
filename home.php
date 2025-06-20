<?php
require_once('sessionStuff.php');
require_once('dbStuff.php');

$pageTitle = "Home";

// Fetch recent posts (from all users)
$stmt = $db->prepare("
  SELECT p.*, u.username, u.profile_picture,
         GROUP_CONCAT(DISTINCT t.name SEPARATOR ', ') AS topic_names
  FROM follows f
  JOIN posts p ON p.user_id = f.following_id
  JOIN users u ON p.user_id = u.id
  LEFT JOIN topicmessages tm ON p.id = tm.post_id
  LEFT JOIN topics t ON tm.topic_id = t.id
  WHERE f.follower_id = ?
  GROUP BY p.id
  ORDER BY p.created_at DESC
  LIMIT 20
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$posts = $stmt->get_result();


include("header.php");
?>

<body>
  <?php include("topbar.php"); ?>
  <?php include("sidebar.php"); ?>

  <main>
    <div class="content-container">
      <div class="center-column">
        <h2>Latest Posts</h2>
        <?php while ($post = $posts->fetch_assoc()): ?>
          <?php
          $userVote = null;
          if (isset($_SESSION['user_id'])) {
            $voteCheck = $db->prepare("SELECT vote_value FROM votes WHERE user_id = ? AND post_id = ?");
            $voteCheck->bind_param("ii", $_SESSION['user_id'], $post['id']);
            $voteCheck->execute();
            $voteCheck->bind_result($userVote);
            $voteCheck->fetch();
            $voteCheck->close();
          }
          ?>
          <div class="post">
            <div class="post-meta">
              <img src="<?php echo htmlspecialchars($post['profile_picture']); ?>" class="profile-pic-header">
              <strong>
                <a href="user.php?username=<?php echo urlencode($post['username']); ?>">
                  <?php echo htmlspecialchars($post['username']); ?>
                </a>
              </strong>
              <?php
              $topicList = !empty($post['topic_names']) ? explode(',', $post['topic_names']) : ['General'];
              foreach ($topicList as $topic) {
                echo " · <a class='post-topic-tag' href='topic.php?topic=" . urlencode(trim($topic)) . "'>" . htmlspecialchars(trim($topic)) . "</a>";
              }
              ?>
              · <?php echo date("Y-m-d H:i", strtotime($post['created_at'])); ?>
            </div>

            <?php if (!empty($post['title'])): ?>
              <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
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
                <button class="vote-btn upvote">
                  <span
                    class="material-symbols-outlined <?php echo $userVote === 1 ? 'active-vote' : ''; ?>">arrow_upward</span>
                </button>
              </form>

              <span><?php echo $post['vote']; ?></span>

              <form action="vote.php" method="POST">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <input type="hidden" name="vote_change" value="-1">
                <button class="vote-btn downvote">
                  <span
                    class="material-symbols-outlined <?php echo $userVote === -1 ? 'active-vote' : ''; ?>">arrow_downward</span>
                </button>
              </form>

              <a href="thread.php?post_id=<?php echo $post['id']; ?>" title="Comment">
                <span class="material-symbols-outlined">chat_bubble</span>
              </a>

              <button type="button" class="share-button" data-post-id="<?php echo $post['id']; ?>"
                title="Copy share link">
                <span class="material-symbols-outlined">link</span>
              </button>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  </main>

  <div id="copy-toast" class="copy-toast">Link copied!</div>
  <script>
    function showCopyToast() {
      const toast = document.getElementById('copy-toast');
      toast.classList.add('show');
      setTimeout(() => {
        toast.classList.remove('show');
      }, 1500);
    }

    document.querySelectorAll('.share-button').forEach(button => {
      button.addEventListener('click', () => {
        const postId = button.dataset.postId;
        const shareUrl = `${window.location.origin}/thread.php?post_id=${postId}`;
        const icon = button.querySelector('.material-symbols-outlined');

        navigator.clipboard.writeText(shareUrl).then(() => {
          showCopyToast();
          icon.classList.add("share-copied");
          setTimeout(() => {
            icon.classList.remove("share-copied");
          }, 1500);
        }).catch(err => {
          alert("Failed to copy link.");
          console.error(err);
        });
      });
    });
  </script>
</body>

</html>