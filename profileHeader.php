<?php
  require_once('dbStuff.php');
  require_once('sessionStuff.php');
  require_once 'fancy.php';

  $user_id;
  $userdata;
  $isOwnPage;
  $isFollowing;
  $pageTitle;
  $follows;

  //Check wether this is a user page pr a topic's page
  if (isset($_GET['username']))
  {
    $user_id = getUserIDFromUsername($_GET['username']);
    $result = findSomeone('', $_GET['username']);
    if (!$result[0]) {
        header("Location: home.php");
        exit();
    }

    //Get the profile page viewer's info
    $userdata = getUserData($user_id);
    //Check whether this is own user's page
    $isOwnPage = getUserIDFromUsername($_SESSION['myUsername']) == $user_id;
    //Check whether we are following this user
    $isFollowing = !$isOwnPage && checkFollowing($user_id, getUserIDFromUsername($_SESSION['myUsername']));
    //Page title, (the one shown in the tab above)
    $pageTitle = htmlspecialchars($userdata['username']) . "'s Profile";

    $follows = getFollowCount($user_id);
  }
  else if (isset($_GET['topic']))
  {
    //Topic page stuff
  }
  else
  {
    //If we are here, somebody did something very wrong, sent thewm to the naughty corner.
    header("Location: home.php");
    exit();
  }
?>

<!-- ------------------ PROFILE HEADER ------------------ -->
<div class="profile-header">
    <div class="profile-left">
        <div class="profile-pic-container">
            <img src="<?php 
                        //Check if profile pic is set or of it exists
                        $pfp = $userdata['profile_picture'] ?? 'images/blank.jpg';
                        if (!file_exists($pfp))
                            $pfp = 'images/blank.jpg';
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
            <span class="stats-count"><?php echo $follows[1]?></span>
        </div>
        <div class="stats-box">
            <span class="stats-label">Following</span>
            <span class="stats-count"><?php echo $follows[0]?></span>
        </div>
    </div>
</div>
