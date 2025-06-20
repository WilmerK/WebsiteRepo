<?php
require_once("config.php");
//Connect to db
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name) or die();

//Logs in user (creates a new session)
function loginUser($username, $password)
{
	global $db;

	// Prepare queries to check username or email
	$q1 = $db->prepare("SELECT id, username, password_hash, salt FROM users WHERE username = ?");
	$q1->bind_param("s", $username);
	$q1->execute();
	$result1 = $q1->get_result();

	$q2 = $db->prepare("SELECT id, username, password_hash, salt FROM users WHERE email = ?");
	$q2->bind_param("s", $username);
	$q2->execute();
	$result2 = $q2->get_result();

	// Dummy query
	mysqli_query($db, "SELECT * FROM users WHERE username = 'thisisnotauserandisonlyadummy'");

	// Choose result
	if ($result1->num_rows === 1)
		$result = $result1;
	else if ($result2->num_rows === 1)
		$result = $result2;
	else
		return FALSE;

	// Check password
	$row = $result->fetch_assoc();
	$pw = sha1($password . $row['salt']);
	if ($pw !== $row['password_hash']) {
		return FALSE;
	}

	// Generate session ID
	$sessionid = sha1($row['salt'] . $_POST['password'] . microtime() . rand());

	// Check for existing session
	$q = $db->prepare("SELECT * FROM sessions WHERE username = ?");
	$q->bind_param("s", $username);
	$q->execute();
	$existing = $q->get_result();

	$timeNow = time();
	if ($existing->num_rows !== 0) {
		$q = $db->prepare("UPDATE sessions SET session_id = ?, timeLastSeen = ? WHERE username = ?");
		$q->bind_param("sis", $sessionid, $timeNow, $username);
		$q->execute();
	} else {
		$q = $db->prepare("INSERT INTO sessions (session_id, username, timeLastSeen) VALUES (?, ?, ?)");
		$q->bind_param("ssi", $sessionid, $username, $timeNow);
		$q->execute();
	}

	// Return everything needed
	return [
		'session_id' => $sessionid,
		'id' => $row['id'],
		'username' => $row['username']
	];
}


//Creates a new user account
function createUser($email, $username, $password)
{
	global $db;
	//Creates the salt used to further encrypt the users' stuff
	$salt = substr(sha1($email), 0, 10);

	//password encrypted
	$password = sha1($password . $salt);
	//Prepares all strings to be inserted into the database
	$q = $db->prepare("INSERT INTO users (username, email, password_hash, salt) VALUES (?, ?, ?, ?)");
	//Creates and inserts user into table
	$q->bind_param("ssss", $username, $email, $password, $salt);
	if ($q->execute())
		return true;
	return false;

}

//Finds if a user exists
function findSomeone($email = '', $username = '')
{
	global $db;
	$q = '';
	//If we are looking for an email
	if ($email != '') {
		$q = $db->prepare("SELECT email FROM users WHERE email = ?");
		$q->bind_param("s", $email);
	}
	//If we are looking for a username
	else if ($username != '') {
		$wildcard = "%$username%";
		$q = $db->prepare("SELECT username FROM users WHERE username LIKE ?");
		$q->bind_param("s", $wildcard);
	}

	//Execute the query
	$q->execute();
	//Get the result
	$result = $q->get_result();
	//If we have found what we are looking for
	if ($result->num_rows != 0) {
		$row = $result->fetch_assoc();
		if ($email == '')
			return [true, $row['username']];
		return [true, $row['email']];
	}
	//If we did not find someone
	return [false, ''];
}

function getDatabase()
{
	require 'config.php';
	$dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
	try {
		return new PDO($dsn, $db_user, $db_pass, [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		]);
	} catch (PDOException $e) {
		die("Database connection failed: " . $e->getMessage());
	}
}

//Gets information for user's page
function getUserData($userid)
{
	global $db;
	$q = $db->prepare("SELECT username, full_name, bio, profile_picture FROM users WHERE id = ?");
	$q->bind_param("i", $userid);
	$q->execute();
	$result = $q->get_result();

	if ($result->num_rows != 0) {
		$row = $result->fetch_assoc();
		return $row;
	}
	return [];
}

//Get the follow count for user
function getFollowCount($userid)
{
	global $db;
	$q = $db->prepare("SELECT
  					  (SELECT COUNT(*) FROM follows WHERE following_id = ?) AS followers,
  					  (SELECT COUNT(*) FROM follows WHERE follower_id = ?) AS following;");
	$q->bind_param("ii", $userid, $userid);
	$q->execute();
	$result = $q->get_result();

	//Check is any data was retrieved
	if ($result->num_rows != 0)
	{
		$row = $result->fetch_assoc();
		return [$row['following'], $row['followers']];
	}
	return [0, 0];
}

function changeFollower($follower_id, $following_id)
{
	global $db;
	//Check if the user is already following
	$follows = false;
	$q = $db->prepare("SELECT * FROM follows WHERE follower_id = ? AND following_id = ?");
	$q->bind_param("ii", $follower_id, $following_id);
	$q->execute();
	$res = $q->get_result();
	if ($res->num_rows != 0)
		$follows = true;
	
	// If a user wants to follow
	if (!$follows)
	{
		$q = $db->prepare("INSERT INTO follows (follower_id, following_id) VALUES (?, ?)");
		$q->bind_param("ii", $follower_id, $following_id);
	// If a user want to unfollow
	}else{
		$q = $db->prepare("DELETE FROM follows WHERE follower_id = ? and following_id = ?");
		$q->bind_param("ii", $follower_id, $following_id);
	}

	//Fault finding
	if ($q->execute())
		return true;
	return false;
}

//Check if a user is following another user.
function checkFollowing($follower_id, $following_id)
{
	global $db;
	$q = $db->prepare("SELECT * FROM follows WHERE follower_id = ? AND following_id = ?");
	$q->bind_param("ii", $follower_id, $following_id);
	$q->execute();
	$result = $q->get_result();
	if ($result->num_rows != 0)
		return true;
	return false;
}

//Get userid from username.
function getUserIDFromUsername($username)
{
	global $db;
	$q = $db->prepare("SELECT id FROM users WHERE username = ?");
	$q->bind_param("s", $username);
	$q->execute();
	$result = $q->get_result();
	if ($result->num_rows != 0) {
		$row = $result->fetch_assoc();
		return $row['id'];
	}
	return NULL;
}

//Get username from userid
function getUsernameFromID($userid)
{
	global $db;
	$q = $db->prepare("SELECT username FROM users WHERE id = ?");
	$q->bind_param("s", $userid);
	$q->execute();
	$result = $q->get_result();
	if ($result->num_rows != 0) {
		$row = $result->fetch_assoc();
		return $row['username'];
	}
	return NULL;
}

//Check whether a session is valid
function checkSessionInDB($sessionid)
{
	global $db;
	$q = $db->prepare("SELECT * FROM sessions WHERE session_id = ?");
	$q->bind_param("s", $sessionid);
	$q->execute();
	$result = $q->get_result();

	if ($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		return $row['timeLastSeen'];
	} else {
		return '';
	}
}


//Get username from a session id
function getUserFromSession($sessionid)
{
	global $db;
	//Looks for session
	$q = $db->prepare("SELECT * FROM sessions WHERE session_id = ?");
	$q->bind_param("s", $sessionid);
	$q->execute();
	$result = $q->get_result();
	//If the sessionif was found, return true
	if ($result->num_rows == 1)
		return $result;
	//If the session was not found, return false
	return '';
}

//Clears a session from the sessions table
function clearSession($sessionid)
{
	global $db;
	//Looks for session
	$q = $db->prepare("DELETE FROM sessions WHERE session_id = ?");
	$q->bind_param("s", $sessionid);
	$q->execute();
	return;
}

function updateSessionTime($sessionid)
{
	global $db;
	$q = $db->prepare("UPDATE sessions SET timeLastSeen = ? WHERE session_id = ?");
	$timeNow = time();
	$q->bind_param("is", $timeNow, $sessionid);
	$q->execute();
	return;
}

function getUserPosts($userid)
{
	global $db;
	$q = $db->prepare("
		SELECT 
			posts.*, 
			GROUP_CONCAT(DISTINCT topics.name SEPARATOR ', ') AS topic_names
		FROM posts
		LEFT JOIN topicmessages ON posts.id = topicmessages.post_id
		LEFT JOIN topics ON topicmessages.topic_id = topics.id
		WHERE posts.user_id = ?
		GROUP BY posts.id
		ORDER BY posts.created_at DESC
	");

	$q->bind_param('i', $userid);
	$q->execute();
	$result = $q->get_result();
	return $result->fetch_all(MYSQLI_ASSOC);
}


function postContent($userid, $title, $media_url, $caption, $tags)
{
	global $db;

	// Insert post
	$q = $db->prepare("INSERT INTO posts (user_id, title, caption, media_url, vote) VALUES (?, ?, ?, ?, 0)");
	$q->bind_param("isss", $userid, $title, $caption, $media_url);
	$q->execute();
	$post_id = $q->insert_id;

	// Handle tags
	if (!empty($tags)) {
		foreach ($tags as $tag) {
			$tag = strtolower(trim($tag));
			// Find or insert topic
			$res = findTopic($tag);
			if (!$res[0]) {
				$q = $db->prepare("INSERT INTO topics (name) VALUES (?)");
				$q->bind_param("s", $tag);
				$q->execute();
				$res = findTopic($tag); // Refresh to get ID
			}
			$topic_id = $res[1];

			// Insert into topicmessages
			$q = $db->prepare("INSERT INTO topicmessages (topic_id, post_id) VALUES (?, ?)");
			$q->bind_param("ii", $topic_id, $post_id);
			$q->execute();
		}
	} else {
		// Default to "general"
		$tag = "general";
		$res = findTopic($tag);
		if (!$res[0]) {
			$q = $db->prepare("INSERT INTO topics (name) VALUES (?)");
			$q->bind_param("s", $tag);
			$q->execute();
			$res = findTopic($tag);
		}
		$topic_id = $res[1];

		$q = $db->prepare("INSERT INTO topicmessages (topic_id, post_id) VALUES (?, ?)");
		$q->bind_param("ii", $topic_id, $post_id);
		$q->execute();
	}
}


//Looks for a topic
function findTopic($topic)
{
	global $db;
	$q = $db->prepare("SELECT * FROM topics WHERE name = ?");
	$q->bind_param("s", $topic);
	$q->execute();
	$result = $q->get_result();
	if ($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		return [true, $row['id']];
	}
	return [false, ''];
}

//Update user info
function updateUserInfo($userid, $username, $fullName, $bio, $profilePic)
{
	global $db;
	//Delete old profile picture (if it exists)
	$userData = getUserData($userid);
	$usrName = $userData['username'];
	$pfp = $userData['profile_picture'] ?? 'images/blank.jpg';

	//If the two profile pics match do nothing
	if ($pfp != $profilePic) {
		//Checks if the pfp is still there
		if (file_exists($pfp)) {
			//Just checks if it can delete (its no prob in short term if it cannot be deleted)
			if (unlink($pfp)) {
				NULL;
			}
		}
	}

	//If the two usernames do not match, we have to update some session stuff.
	if ($usrName != $username) {
		//Update username in sessions table (TBH, SESSIONS WOULD BE A LOT EASIER IF WE JUST USED THE USER_ID)
		$q = $db->prepare("UPDATE sessions SET username = ? WHERE username = ?");
		$q->bind_param("ss", $username, $usrName);
		$q->execute();
	}

	//Update user information in user table
	$q = $db->prepare("UPDATE users SET username = ?, full_name = ?, bio = ?, profile_picture = ? WHERE id = ?");
	$q->bind_param("ssssi", $username, $fullName, $bio, $profilePic, $userid);
	$q->execute();
}

//Deletes a post with a certain id
function deletePost($postid)
{
	global $db;
	$q = $db->prepare("DELETE FROM posts WHERE id = ?");
	$q->bind_param("i", $postid);
	//Checks if the query executed
	if ($q->execute())
		return true;
	return false;
}

// Get all information of the post (with credit functionality)
function getPostInfo($postid, $userid)
{
	global $db;
	//We want to fine oput if the post belongs to the user
	if ($userid != NULL)
	{
		$q = $db->prepare("SELECT title, media_url, caption, created_at FROM posts WHERE id = ? AND user_id = ?");
		$q->bind_param("ii", $postid, $userid);
	}
	else
	{
		$q = $db->prepare("SELECT title, media_url, caption, created_at FROM posts WHERE id = ?");
		$q->bind_param("i", $postid);
	}	
	$q->execute();
	$res = $q->get_result();
	//Check if the post exists
	if ($res->num_rows != 0)
		return [true, $res->fetch_assoc()];
	return [false, ''];
}

//Get all topics related to a post
function getPostTopics($postid)
{
	global $db;
	$q = $db->prepare("SELECT topic_id FROM topicmessages WHERE post_id = ?");
	$q->bind_param("i", $postid);
	$q->execute();
	$res = $q->get_result();
	if ($res->num_rows != 0)
	{
		// fetch_all returns an array of rows (as numeric arrays by default)
		$allRows = $res->fetch_all(MYSQLI_ASSOC); 

		// Extract column values
		return [true, $columnValues = array_column($allRows, 'topic_id')];
	}
	return [false, ''];
}

//Get the amount of posts with this topic
function getTopicPostCount($topicid)
{
	global $db;
	$q = $db->prepare("SELECT count(*) FROM topicmessages WHERE topic_id = ?");
	$q->bind_param("i", $topicid);
	$q->execute();
	$res = $q->get_result();
	if ($res->num_rows != 0)
	{
		$row = $res_>fetch_assoc();
		return [true, $row];
	}
	return [false, ''];
}

//Deletes a topic
function deleteTopic($topicid)
{
	global $db;
	$q = $db->prepare("DELETE FROM topics WHERE id = ?");
	$q->bind_param("i", $topicid);
	//Checks if the query executed
	if ($q->execute())
		return true;
	return false;
}

//This will return the top 5 of each heading on the sidebar
function sidebarPopulate($userid)
{

}

//Get the number of followers per topic
function getTopicFollowCount()
{

}
?>