<?php
    require_once('dbStuff.php');
    require_once('sessionStuff.php');

    $errorMsg = '';

    $user_id = getUserIDFromUsername($_SESSION['myUsername']);
    $userData = getUserData($user_id);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Check is the cancel button was pressed.
        if (isset($_POST['btnCancel']))
        {
            header("Location: user.php?username=".$_SESSION['myUsername']);
            exit();
        }

        //Do some editing here
        $username = $_POST['username'];
        $fullName = $_POST['fullName'];
        $bio = $_POST['bio'];
        $media_url = $userData['profile_picture'] ?? 'images/blank.jpg';
        $userid = getUserIDFromUsername($username);
        $uploadDir = 'profilePictures/';

        //Check if there is a profile pic to upload
        if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
            $filename = basename($_FILES['media']['name']);

            //If the profilepic has not changed, do nothing
            if ($filename != $media_url)
            {
                $targetFile = $uploadDir . time() . '_' . $filename;
                if (!move_uploaded_file($_FILES['media']['tmp_name'], $targetFile)) {
                    $errorMsg = "Something went wrong, please try again.";
                    exit();
                }
                $media_url = $targetFile;
            }
        }

        //Upload changes to db
        updateUserInfo($userid, $username, $fullName, $bio, $media_url);
        $_SESSION['myUsername'] = $username;

        //Return to user page
        header('Location: user.php?username='.$username);
        exit();
    }

    //Get user data in separate variables
    $username = $userData['username'];
    $fullName = $userData['full_name'] ?? '';
    $bio = $userData['bio'] ?? '';
    //n Weird ding het hier gebeur lol
    $media_url = $userData['profile_picture'] ?? '';

    //If the user does not have a profilepic
    if ($media_url == '')
        //The default no profile picture picture
        $media_url = 'images/blank.jpg';
    
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>4chen - Post Media</title>
  <style>
    * {
      box-sizing: border-box;
    }

    html,
    body {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: Arial, sans-serif;
      background-color: #fafafa;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .content-box {
      background-color: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      width: 500px;
      height: 670px;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .embedded-header {
      background-color: #242526;
      color: white;
      height: 20%;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0 20px;
    }

    .top-center {
      display: flex;
      align-items: center;
      gap: 15px;
      font-weight: bold;
      font-size: 1.75rem;
      user-select: none;
    }

    .logo {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      display: inline-block;
    }

    form.update-form {
      height: 80%;
      padding: 20px 30px;
      display: flex;
      flex-direction: column;
      box-sizing: border-box;
    }

    .media-preview-container {
      flex: 1;
      border: 2px dashed #ccc;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 15px;
      position: relative;
      overflow: hidden;
      background-color: #f9f9f9;
      height: 200px;
    }

    .media-preview-container img,
    .media-preview-container video {
      max-width: 100%;
      max-height: 100%;
      border-radius: 8px;
      object-fit: contain;
    }

    input[type="file"] {
      display: none;
    }

    .btn-browse {
      background-color: #FF4500;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      align-self: flex-start;
      margin-bottom: 10px;
      transition: background-color 0.3s ease;
    }

    .btn-browse:hover {
      background-color: #e03e00;
    }

    input[type="text"],
    textarea {
      width: 100%;
      border-radius: 6px;
      border: 1px solid #ccc;
      padding: 8px 10px;
      font-size: 14px;
      margin-bottom: 12px;
      font-family: Arial, sans-serif;
      resize: vertical;
    }

    textarea {
      min-height: 50px;
      max-height: 100px;
    }

    .input-label {
      font-weight: bold;
      margin-bottom: 4px;
      font-size: 14px;
      user-select: none;
    }

    .button-row {
      display: flex;
      gap: 15px;
      justify-content: flex-end;
      margin-top: auto;
    }

    .btn-cancel,
    .btn-post {
      flex: 1;
      padding: 12px 0;
      border-radius: 6px;
      font-weight: 600;
      font-size: 16px;
      cursor: pointer;
      border: none;
      transition: background-color 0.3s ease;
    }

    .btn-cancel {
      background-color: #ccc;
      color: #333;
    }

    .btn-cancel:hover {
      background-color: #b3b3b3;
    }

    .btn-post {
      background-color: #FF4500;
      color: white;
    }

    .btn-post:hover {
      background-color: #e03e00;
    }
  </style>
</head>

<body>
  <main>
    <div class="content-box">
      <div class="embedded-header">
        <div class="top-center">
          <img class="logo" src="images/4chenlogo.jpg" alt="Logo" />
          <span>4chen</span>
        </div>
      </div>
      <form method="post" enctype="multipart/form-data" class="update-form" id="updateForm">

        <label type="button" class="btn-browse" for="mediaInput">Browse Media</label>
        <input type="file" id="mediaInput" name="media" accept="image/*" />

        <div class="media-preview-container" id="mediaPreviewContainer">
          <img src=<?php echo $media_url ?>></img>
        </div>

        <label class="input-label" for="username">Username</label>
        <input type="text" id="username" name="username" required value=<?php echo $username ?>></input>

        <label class="input-label" for="fullName">Full name</label>
        <input type="text" id="fullName" name="fullName" value="<?php echo $fullName ?>"></input>

        <label class="input-label" for="bio">Bio</label>
        <textarea id="bio" name="bio"><?php echo $bio?></textarea>
        <label id="lblError" style="color: red;"><?php echo $errorMsg?></label>

        <div class="button-row">
          <button name="btnCancel" type="submit" class="btn-cancel" formnovalidate>Cancel</button>
          <button type="button" class="btn-post" id="btnSave">Save</button>
        </div>
      </form>
    </div>
  </main>
  <script>
    const mediaInput = document.getElementById('mediaInput');
    const mediaPreviewContainer = document.getElementById('mediaPreviewContainer');
    //Max file size is 5mb
    const MAX_FILE_SIZE_MB = 5;
    const MAX_FILE_SIZE = MAX_FILE_SIZE_MB * 1024 * 1024;

    //Each time the pfp is updated.
    mediaInput.addEventListener('change', () => {
      const file = mediaInput.files[0];
      //Clear previous preview
      mediaPreviewContainer.innerHTML = ''; 

      //If no file was selected.
      //Assuming this is the filepath + name etc.      
      if (!file) {
        mediaPreviewContainer.innerHTML = '<span style="color:#999;">No media selected</span>';
        return;
      }

      //If the file size was larger than the max size
      if (file.size > MAX_FILE_SIZE) {
        mediaPreviewContainer.innerHTML = `<span style="color:red;">File is too large. Max size is ${MAX_FILE_SIZE_MB} MB.</span>`;
        mediaInput.value = '';
        return;
      }

      const fileType = file.type;

      //Checks if filetype is image
      if (fileType.startsWith('image/')) {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.onload = () => URL.revokeObjectURL(img.src);
        mediaPreviewContainer.appendChild(img);
      } else {
        mediaPreviewContainer.innerHTML = '<span style="color:red;">Unsupported media type.</span>';
      }
    });

    document.getElementById('btnSave').addEventListener("click", function(){
        //Check if the username is still good.
        //Gets username
        username = document.getElementById('username');
        //Checks if username is empty
        if (!username.checkValidity())
        {
            username.value = "";
            lblError.textContent = "Username can't be empty."
            return;
        }
        //Check is username contains strange characters.
        pattern = /^[a-zA-Z0-9_]+$/;
        if (!pattern.test(username.value))
        {
            lblError.textContent = "Username can only contain letters, numbers and underscores."
            username.value = "";
            return;
        }

        //Check if the full name does not contain any strange characters
        //Gets full name
        fullName = document.getElementById('fullName');
        if (fullName.value != '')
        {
            //Check is username contains strange characters.
            pattern = /^[a-zA-Z ]+$/;
            if (!pattern.test(fullName.value))
            {
                lblError.textContent = "Name can only contain letters, numbers and underscores."
                fullName.value = "";
                return;
            }
        }

        //Check if the bio does not contain any strange characters
        //Gets bio
        bio = document.getElementById('bio');
        if (bio.value != '')
        {
            //Checks if bio has extra weird chars
            pattern = /^[a-zA-Z0-9@#$%^&*!_\-+=\n ]+$/;
            if (!pattern.test(bio.value))
            {
                lblError.textContent = "Bio caintain's strange(er) characters."
                bio.value = "";
                return;
            }
        }

        //If the code gets to this point, we can submit.
        document.getElementById("updateForm").submit();
    });
  </script>
</body>