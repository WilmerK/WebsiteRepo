<?php
require_once('dbStuff.php');
//This checks the session stuff
require_once('sessionStuff.php');
$errorMsg = '';

//Checks if the incoming requestx is a post.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['btnCancel'])) {
    header("Location: user.php?username=".$_SESSION['myUsername']);
    exit();
  }

  //Obtain all the variables
  $title = $_POST['title'];
  $caption = $_POST['caption'];
  $tags = $_POST['tags'];
  $media_url = null;

  // Handle media upload if provided
  //Save the media into the user's own folder in the name concerning his/her post id.
  $username = $_SESSION['myUsername'];
  $userid = getUserIDFromUsername($username);
  $uploadDir = 'uploads/'.$username.'/';
  //Check if the directory exists
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
  }

  //Check if there is media to upload
  if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
    $fileName = basename($_FILES['media']['name']);
    $targetFile = $uploadDir . time() . '_' . $fileName;
    if (!move_uploaded_file($_FILES['media']['tmp_name'], $targetFile)) {
      $errorMsg = "Something went wrong, please try again.";
      exit();
    }
    $media_url = $targetFile;
  }

  //Split get the tags here and split them
  $tags = array_map('trim', explode(',', $_POST['tags']));

  //Upload post to db
  postContent($userid, $title, $media_url, $caption, $tags);

  // Redirect back to user profile
  header("Location: user.php?username=" . $_SESSION['myUsername']);
  exit();
}
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

    form.post-form {
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
      <form method="post" enctype="multipart/form-data" class="post-form" id="postForm">
        <label class="input-label" for="titleInput">Post Title</label>
        <input type="text" id="titleInput" name="title" placeholder="Enter a title..." required />

        <label type="button" class="btn-browse" for="mediaInput">Browse Media</label>
        <input type="file" id="mediaInput" name="media" accept="image/*,video/*" />

        <div class="media-preview-container" id="mediaPreviewContainer">
          <span style="color:#999; font-size: 16px;">No media selected</span>
        </div>

        <label class="input-label" for="captionInput">Edit caption</label>
        <textarea id="captionInput" name="caption" placeholder="Write a caption..."></textarea>

        <label class="input-label" for="tagsInput">Edit tags</label>
        <textarea id="tagsInput" name="tags" placeholder="Add tags, separated by commas"></textarea>
        <label id="lblError" style="color: red;"><?php echo $errorMsg?></label>

        <div class="button-row">
          <button name="btnCancel" type="submit" class="btn-cancel" formnovalidate>Cancel</button>
          <button type="button" class="btn-post" id="btnPost">Post</button>
        </div>
      </form>
    </div>
  </main>

  <script>
    const mediaInput = document.getElementById('mediaInput');
    const mediaPreviewContainer = document.getElementById('mediaPreviewContainer');
    const btnPost = document.getElementById('btnPost');
    //Max file size is 50mb
    const MAX_FILE_SIZE_MB = 50;
    const MAX_FILE_SIZE = MAX_FILE_SIZE_MB * 1024 * 1024;

    //Each time the media is updated.
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
        //Checks if filetype is video
      } else if (fileType.startsWith('video/')) {
        const video = document.createElement('video');
        video.src = URL.createObjectURL(file);
        video.controls = true;
        video.onloadeddata = () => URL.revokeObjectURL(video.src);
        mediaPreviewContainer.appendChild(video);
        //No other filetypes are accepted
      } else {
        mediaPreviewContainer.innerHTML = '<span style="color:red;">Unsupported media type.</span>';
      }
    });

    btnPost.addEventListener('click', function(){
      //Get title edit
      const edtTitle = document.getElementById('titleInput');
      const lblError = document.getElementById('lblError');

      //If the title is empty, then raas with user
      if (edtTitle.value.trim() == '')
      {
        lblError.textContent = "Title cannot be empty.";
        return;
      }
      
      //Checks if title contains strange characters
      pattern = /^[a-zA-Z0-9 !?,.;&%$#@'"]+$/;
      if (!pattern.test(edtTitle.value))
      {
        edtTitle.value = '';
        lblError.textContent = "Title contains strange characters."
        return;
      }

      //If the code gets to this point, we can submit.
      document.getElementById("postForm").submit()
    });
  </script>
</body>

</html>