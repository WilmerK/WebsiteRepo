<?php
require_once('dbStuff.php');
require_once('sessionStuff.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'], $_SESSION['user_id'])) 
{
    $post_id = intval($_POST['post_id']);
    $user_id = $_SESSION['user_id'];

    //Get the topic(s) of the post
    $res = getPostTopics($post_id);
    $topics;
    //If not false lol
    if (!$res[0])
        $topics = $res[1];
    
    $res = getPostInfo($post_id, $user_id);
    //If the user does own the post, delete it
    if ($res[0])
        //Table is set up to delete on cascade
        if (deletePost($post_id))
        {
            $postData = $res[1];
            //Deletes the media if it exists
            if (!empty($postData['media_url']) && file_exists($postData['media_url'])) {
                unlink($postData['media_url']);
            }
        }

    //Loop through each topic, and check if there are any posts left in it, if not delete the topic
    foreach ($topics as $topic_id) {
        $res = getTopicPostCount($topic_id);
        //If the fucntion ran correctly
        if ($res[0])
            //If there are no more posts within the topic, delete it
            if ($res[1] == 0)
                deleteTopic($topic_id);
    }
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
