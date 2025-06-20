<?php
// ==========================================================
// UPDATED USER PROFILE PAGE
// ==========================================================
require_once('dbStuff.php');
require_once('sessionStuff.php');
require 'fancy.php';

// ------------------ Get Target Page User ------------------
if (!isset($_GET['username'])) {
    header("Location: home.php");
    exit();
}

$user_id = getUserIDFromUsername($_GET['username']);
$result = findSomeone('', $_GET['username']);
if (!$result[0]) {
    header("Location: home.php");
    exit();
}
//Used for top rigth profile pic
$myUserData = getUserData($_SESSION['user_id']);

//Used for page's user
$userdata = getUserData($user_id);
$follows = getFollowCount($user_id);
$isOwnPage = $_SESSION['user_id'] == $user_id;
$isFollowing = !$isOwnPage && checkFollowing($_SESSION['user_id'], $user_id);
$posts = getUserPosts($user_id);
$pageTitle = htmlspecialchars($userdata['username']) . "'s Profile";

include('header.php');
?>

<body>
    <?php include('topbar.php'); ?>
    <?php include('sidebar.php'); ?>

    <main>
        <div class="content-container">
            <?php
            //This is to ensure that interal php vars dont leak into external php
            function loadPageHeader()
            {
                require_once('profileHeader.php');
            }
            loadPageHeader();
            ?>

            <!-- ------------------ CREATE POST BUTTON ------------------ -->
            <?php if ($isOwnPage): ?>
                <button id="btnPost" class="create-post-btn">Create New Post</button>
            <?php endif; ?>

            <!-- ------------------ FOLLOW/UNFOLLOW BUTTON -------------- -->
            <?php if (!$isOwnPage): ?>
                <form action="follow.php" method="POST">
                    <input type="hidden" name="target_id" value="<?php echo $user_id; ?>">
                    <button type="submit" class="create-post-btn"
                        style="<?php echo $isFollowing ? "background-color: #888;" : "background-color: #FF4500;" ?>">
                        <?php echo $isFollowing ? 'Unfollow' : 'Follow'; ?>
                    </button>
                </form>
            <?php endif; ?>

            <div class="center-column">
                <!-- ------------------ POST FEED ------------------ -->
                <?php foreach ($posts as $post): ?>
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
                            <?php
                            $topicList = !empty($post['topic_names']) ? explode(',', $post['topic_names']) : ['General'];
                            foreach ($topicList as $topic) {
                                $topic = trim($topic);
                                $safeTopic = htmlspecialchars($topic);
                                echo "<a class='post-topic-tag' href='topic.php?topic=" . urlencode($topic) . "'>$safeTopic</a> ";
                            }
                            echo '– ' . date("Y-m-d H:i", strtotime($post['created_at']));
                            ?>
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

                        <div class="post-actions" data-post-id="<?php echo $post['id']; ?>">
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

                            <?php if ($_SESSION['user_id'] == $post['user_id']): ?>
                                <form action="deletePost.php" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this post?');"
                                    style="display:inline;">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" title="Delete Post">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <script>
        //First just check if this exists
        if (document.getElementById('btnPost'))
            document.getElementById('btnPost')?.addEventListener('click', function () {
                window.location.href = 'createPost.php';
            });


        //First check if eleemnt exists
        if (document.getElementById('btnEditProfile'))
            document.getElementById('btnEditProfile').addEventListener('click', function () {
                window.location.href = 'editUser.php';
            });
    </script>
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