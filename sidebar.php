<!-- sidebar.php -->
<aside class="sidebar">
  <h3>People you follow</h3>
  <ul>
    <?php
    $uid = $_SESSION['user_id'];
    $q = $db->prepare("
  SELECT u.username FROM follows f
  JOIN users u ON f.following_id = u.id
  WHERE f.follower_id = ?
  ORDER BY u.username ASC
");
    $q->bind_param("i", $uid);
    $q->execute();
    $res = $q->get_result();
    if ($res->num_rows === 0) {
      echo "<li style='color: #999;'>Not following anyone</li>";
    } else {
      while ($row = $res->fetch_assoc()) {
        $username = htmlspecialchars($row['username']);
        echo "<li><a href='user.php?username=" . urlencode($username) . "'>$username</a></li>";
      }
    }
    ?>
  </ul>


  <h3>Topics you follow</h3>
  <ul>
    <?php
    $q = $db->prepare("
  SELECT t.name FROM topicfollows tf
  JOIN topics t ON tf.topic_id = t.id
  WHERE tf.follower_id = ?
  ORDER BY t.name ASC
");
    $q->bind_param("i", $uid);
    $q->execute();
    $res = $q->get_result();
    if ($res->num_rows === 0) {
      echo "<li style='color: #999;'>Not following any topics</li>";
    } else {
      while ($row = $res->fetch_assoc()) {
        $topicName = htmlspecialchars($row['name']);
        echo "<li><a href='topic.php?topic=" . urlencode($topicName) . "'>$topicName</a></li>";
      }
    }
    ?>
  </ul>


  <h3>Popular People</h3>
  <ul>
    <?php
    $q = $db->query("
  SELECT u.username, COUNT(f.follower_id) AS followers
  FROM users u
  LEFT JOIN follows f ON u.id = f.following_id
  GROUP BY u.id
  ORDER BY followers DESC
  LIMIT 3
");
    while ($row = $q->fetch_assoc()) {
      $username = htmlspecialchars($row['username']);
      echo "<li><a href='user.php?username=" . urlencode($username) . "'>$username</a></li>";
    }
    ?>
  </ul>

  <h3>Trending Topics</h3>
  <ul>
    <?php
    $q = $db->query("
  SELECT t.name, COUNT(tm.post_id) AS post_count
  FROM topics t
  LEFT JOIN topicmessages tm ON t.id = tm.topic_id
  GROUP BY t.id
  ORDER BY post_count DESC
  LIMIT 5
");
    while ($row = $q->fetch_assoc()) {
      $topic = htmlspecialchars($row['name']);
      echo "<li><a href='topic.php?topic=" . urlencode($topic) . "'>$topic</a></li>";
    }
    ?>
  </ul>

</aside>