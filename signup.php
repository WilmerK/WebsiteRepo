<?php
    $errorMsg = '';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Check is the cancel button was pressed.
        if (isset($_POST['btnCancel']))
        {
            header("Location: login.php");
            exit();
        }

        //Include the connection to DB.
        require('dbStuff.php');

        //First check if an email is already registered
        $returned = findSomeone($_POST['email'], '');
        if ($returned[0])
            $errorMsg = $returned[1]." already has a registered account.";
        else
        {
            //Checks if username already exists
            $returned = findSomeone('', $_POST['username']);
            if ($returned[0])
              $errorMsg = $returned[1]." is already taken.";
            else
            {
              //If no email is registered, create the new user.
              $username = $_POST['username'];
              $password = $_POST['password'];
              $email = $_POST['email'];

              //Checks if the user was successfully created
              if (!createUser($email, $username, $password))
                  //If the user was not successfully created, display error message
                  $errorMsg = "Error creating user, please try again.";
              
              else
              {
                  //If the user was successfully created, send email (later)
                  //Change active page to login.
                  header("Location: login.php");
              }
            }
        }
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

    html, body {
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
      height: 600px;
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
      height: 90%;
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
      margin-bottom: 5px;
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
      <form method="post" id="frmSignUp" class="login-form">
        <h2>Create an account.</h2>
        <input id="email" name="email" type="email" placeholder="Email" required />
        <input id="username" name="username" type="text" placeholder="Username" required />
        <input id="password" name="password" type="password" placeholder="Password" required />
        <input id="confirmPassword" type="password" placeholder="Confirm password" required />
        <label id="lblError" style="color: red;"><?php echo $errorMsg?></label>
        <button id="btnSignUp" type="button">Create Account</button>
        <button id="btnCancel" name="btnCancel" type="submit" formnovalidate>Cancel</button>
      </form>
    </div>
  </main>
</body>
<!--At the moment, this code can be seen by the user (and thus hackers, so we need to save this inside a certain file) -->
        <script>
            //Checks if signUp button is pressed.
            document.getElementById("btnSignUp").addEventListener("click", function() {
                //Get the error label to display error messages
                const lblError = document.getElementById('lblError');

                //Gets the email address
                email = document.getElementById('email');
                //Checks if the email is valid
                if (!email.checkValidity())
                {
                    email.value = "";
                    lblError.textContent = "Invalid email address.";
                    return;
                }
                email.value = email.value.toLowerCase();

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

                //Gets password
                password = document.getElementById('password');
                //Checks if password is empty
                if (!password.checkValidity())
                {
                    lblError.textContent = "Password can't be empty.";
                    return;
                }
                //Checks if password has extra weird chars
                pattern = /^[a-zA-Z0-9@#$%^&*!_\-+=]+$/;
                if (!pattern.test(password.value))
                {
                    lblError.textContent = "Password caintain's strange(er) characters."
                    password.value = "";
                    return;
                }

                //Check if the two passwords are the same.
                confPassword = document.getElementById('confirmPassword');
                if (confPassword.value !== password.value)
                {
                    lblError.textContent = "Passwords don't match.";
                    password.value = "";
                    confPassword.value = "";
                    return;
                }

                //If the code gets to this point, we can submit.
                document.getElementById("frmSignUp").submit()
            });
        </script>
</html>
