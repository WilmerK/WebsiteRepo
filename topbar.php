<?php
  require_once('dbStuff.php');
  require_once('sessionStuff.php');

  //Load user pfp
  $userid = $_SESSION['user_id'];
  $userdata = getUserData($userid); 
?>


<!-- topbar.php -->
<header>
  <div class="top-left">
    <a href="home.php" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
      <img class="logo" src="images/4chenlogo.jpg" alt="4chen Logo" />
      <span class="brand">4chen</span>
    </a>
  </div>

  <div class="search-container">
    <form action="search.php" method="GET">
      <input type="text" name="q" class="search-bar" placeholder="Search..." />
    </form>
  </div>

  <a class="top-right" href="user.php?username=<?php echo urlencode($_SESSION['myUsername']); ?>">
    <img src="<?php 
                //Check if profile pic is set or of it exists
                $pfp = $userdata['profile_picture'] ?? 'images/blank.jpg';
                if (!file_exists($pfp))
                    $pfp = 'images/blank.jpg';
                echo $pfp; 
              ?>" alt="Profile" class="profile-pic-header" />
  </a>
</header>