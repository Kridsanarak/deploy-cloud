<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
</head>

<body>
  <div class="login-container">
    <h2>ระบบจัดการอาคาร</h2>
    <h3>Login</h3>
    <form class="login-form" action="login_send2.php" method="post">
      <label for="username">Username:</label>
      <input class="textuser" type="text" id="username" name="username" placeholder="Enter your username" required>

      <label for="password">Password:</label>
      <div class="password-container">
        <input class="textpass" type="password" id="password" name="password" placeholder="Enter your password"
          required>
        <button type="button" id="showPasswordButton" onclick="togglePassword()" style="padding: 10px">
          <i class="fas fa-eye" id="passwordIcon"></i>
        </button>
      </div><br>

      <input class="button" type="submit" value="Login"><br>
    </form>

    <script>
      function togglePassword() {
        var passwordField = document.getElementById("password");
        var passwordIcon = document.getElementById("passwordIcon");

        if (passwordField.type === "password") {
          passwordField.type = "text";
          passwordIcon.classList.remove("fa-eye");
          passwordIcon.classList.add("fa-eye-slash");
        } else {
          passwordField.type = "password";
          passwordIcon.classList.remove("fa-eye-slash");
          passwordIcon.classList.add("fa-eye");
        }
      }

      // Check for login failure and show popup
      window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('login') === 'fail') {
          alert('Invalid username or password');
        }
      };
    </script>
  </div>
</body>

</html>
