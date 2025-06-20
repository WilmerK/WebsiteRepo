<?php
$errorMsg = '';

//Clear previous session
session_start();
session_unset();
session_destroy();

//Start new session
session_start();


require('dbStuff.php');

if (
  array_key_exists('username', $_POST) &&
  array_key_exists('password', $_POST)
) {
  //Checks if the username and password are correct (this can be done after front-end js has run)
  if (($userData = loginUser($_POST['username'], $_POST['password'])) !== FALSE) {
    $_SESSION['sessionid'] = $userData['session_id'];  // If you want to keep your own token
    $_SESSION['user_id'] = $userData['id'];            // 💡 Required for voting!
    $_SESSION['myUsername'] = $userData['username'];

    //Change to homescreen
    header("Location: home.php");
    exit();
  } else
    //If the username and passwords do not match, clear everything and display message
    $errorMsg = 'Incorrect username or password.';
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>4chen</title>
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
      height: 500px;
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

    .login-form {
      height: 80%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 30px;
      width: 100%;
      box-sizing: border-box;
    }

    .login-form h2 {
      margin-bottom: 20px;
      text-align: center;
    }

    .login-form input {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }

    .login-form button {
      width: 100%;
      padding: 10px;
      background-color: #FF4500;
      border: none;
      border-radius: 6px;
      color: white;
      font-size: 16px;
      cursor: pointer;
    }

    .login-form button:hover {
      background-color: #0056b3;
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
      <form method="post" id="frmLogin" class="login-form">
        <h2>Login.</h2>
        <input id="username" name="username" type="text" placeholder="Username/Email" required />
        <input id="password" name="password" type="password" placeholder="Password" required />
        <label id="lblError" style="color: red;"><?php echo $errorMsg ?></label>
        <label>Don't have an account? <a href="SignUp.php">Create one!</a></label>
        <button id="btnLogin" type="button">Login</button>
      </form>
    </div>
  </main>
</body>
<script>
  document.getElementById("btnLogin").addEventListener("click", function () {
    errorMsg = document.getElementById('lblError');
    //Check if the username is empty
    user = document.getElementById('username');
    if (!user.checkValidity()) {
      errorMsg.textContent = "Username can't be empty."
      return;
    }
    //Check if password is empty
    password = document.getElementById('password')
    if (!password.checkValidity()) {
      errorMsg.textContent = "Password can't be empty."
      return;
    }
    //If the code gets to this point, we can submit.
    document.getElementById("frmLogin").submit();
  });
</script>

</html>