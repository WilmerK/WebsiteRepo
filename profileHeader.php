<?php
require_once('dbStuff.php');
require_once('sessionStuff.php');
require_once('fancy.php');

$pageTitle = '';
$isOwnPage = false;
$isFollowing = false;
$follows = null;
$userdata = null;

// ================= USER PAGE =================
if (isset($_GET['username'])) {
    $user_id = getUserIDFromUsername($_GET['username']);
    $result = findSomeone('', $_GET['username']);
    if (!$result[0]) {
        header("Location: home.php");
        exit();
    }

    $userdata = getUserData($user_id);
    $isOwnPage = getUserIDFromUsername($_SESSION['myUsername']) == $user_id;
    $isFollowing = !$isOwnPage && checkFollowing($user_id, getUserIDFromUsername($_SESSION['myUsername']));
    $follows = getFollowCount($user_id);
    $pageTitle = htmlspecialchars($userdata['username']) . "'s Profile";

    // ------------------ USER HEADER ------------------
    ?>
    <div class="profile-header">
        <div class="profile-left">
            <div class="profile-pic-container">
                <img src="<?php
                    $pfp = $userdata['profile_picture'] ?? 'images/blank.jpg';
                    if (!file_exists($pfp)) $pfp = 'images/blank.jpg';
                    echo $pfp;
                ?>" alt="Profile Picture" class="profile-pic" />
                <?php if ($isOwnPage): ?>
                    <button id="btnEditProfile" class="edit-profile-btn">Edit Profile</button>
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($userdata['username']); ?></h2>
                <p><?php echo htmlspecialchars($userdata['full_name'] ?: ''); ?></p>
                <p class="bio"><?php echo htmlspecialchars($userdata['bio'] ?? ''); ?></p>
            </div>
        </div>
        <div class="profile-right">
            <div class="stats-box">
                <span class="stats-label">Followers</span>
                <span class="stats-count"><?php echo $follows[1]; ?></span>
            </div>
            <div class="stats-box">
                <span class="stats-label">Following</span>
                <span class="stats-count"><?php echo $follows[0]; ?></span>
            </div>
        </div>
    </div>
    <?php

// ================= TOPIC PAGE =================
} else if (isset($_GET['topic'])) {
    $topicName = $_GET['topic'];

    $stmt = $db->prepare("SELECT id FROM topics WHERE name = ?");
    $stmt->bind_param("s", $topicName);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) {
        header("Location: home.php");
        exit();
    }

    $topicRow = $res->fetch_assoc();
    $topicId = $topicRow['id'];
    $topic = $topicName;
    $pageTitle = "Topic: $topic";

    $currentUser = $_SESSION['user_id'];
    $stmt = $db->prepare("SELECT * FROM topicfollows WHERE follower_id = ? AND topic_id = ?");
    $stmt->bind_param("ii", $currentUser, $topicId);
    $stmt->execute();
    $res = $stmt->get_result();
    $isFollowingTopic = $res->num_rows > 0;

    // ------------------ TOPIC HEADER ------------------
    ?>
    <div class="profile-header">
        <div class="profile-left">
            <div class="profile-pic-container">
                <img src="https://cdn-icons-png.flaticon.com/512/565/565547.png" alt="Topic Icon" class="profile-pic" />
            </div>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($topic); ?></h2>
                <p class="bio">Discussion tagged with <strong><?php echo htmlspecialchars($topic); ?></strong></p>
            </div>
        </div>
        <div class="profile-right">
            <form action="follow.php" method="POST">
                <input type="hidden" name="topic_id" value="<?php echo $topicId; ?>">
                <button type="submit" class="create-post-btn"
                    style="<?php echo $isFollowingTopic ? 'background-color: #888;' : 'background-color: #FF4500;' ?>">
                    <?php echo $isFollowingTopic ? 'Unfollow Topic' : 'Follow Topic'; ?>
                </button>
            </form>
        </div>
    </div>
    <?php

// ================= FALLBACK =================
} else {
    header("Location: home.php");
    exit();
}
?>
