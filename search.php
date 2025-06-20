<?php
require_once('sessionStuff.php');
require_once('dbStuff.php');

$query = trim($_GET['q'] ?? '');

$matchedUsers = [];
$matchedTopics = [];

if (!empty($query)) {
    $wildcard = "%$query%";

    // Search usernames
    $stmt = $db->prepare("SELECT username, full_name, profile_picture FROM users WHERE username LIKE ?");
    $stmt->bind_param("s", $wildcard);
    $stmt->execute();
    $result = $stmt->get_result();
    $matchedUsers = $result->fetch_all(MYSQLI_ASSOC);

    // Search topics
    $stmt = $db->prepare("SELECT name FROM topics WHERE name LIKE ?");
    $stmt->bind_param("s", $wildcard);
    $stmt->execute();
    $result = $stmt->get_result();
    $matchedTopics = $result->fetch_all(MYSQLI_ASSOC);
}

$pageTitle = "Search: " . htmlspecialchars($query);
include("header.php");
?>
<body>
<?php include("topbar.php"); ?>
<?php include("sidebar.php"); ?>

<main>
  <div class="center-column">
    <h2>Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>

    <!-- Topics -->
    <h3>Topics</h3>
    <?php if (empty($matchedTopics)): ?>
      <p>No topics found.</p>
    <?php else: ?>
      <ul style="list-style: none; padding-left: 0;">
        <?php foreach ($matchedTopics as $topic): ?>
          <li>
            <a class="post-topic-tag" href="topic.php?topic=<?php echo urlencode($topic['name']); ?>">
              <?php echo htmlspecialchars($topic['name']); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <!-- Users -->
    <h3>Users</h3>
    <?php if (empty($matchedUsers)): ?>
      <p>No users found.</p>
    <?php else: ?>
      <ul style="list-style: none; padding-left: 0;">
        <?php foreach ($matchedUsers as $user): ?>
          <li style="margin-bottom: 15px;">
            <a href="user.php?username=<?php echo urlencode($user['username']); ?>"
               style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit;">
              <img src="<?php echo htmlspecialchars($user['profile_picture'] ?? 'images/blank.jpg'); ?>" alt="PFP"
                   style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
              <div>
                <strong><?php echo htmlspecialchars($user['username']); ?></strong><br>
                <small><?php echo htmlspecialchars($user['full_name'] ?? ''); ?></small>
              </div>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</main>
</body>
</html>
