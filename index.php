<?php
session_start();

// ตรวจสอบว่ามีการเลือกภาษาใหม่ผ่าน URL หรือไม่
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// กำหนดภาษาเริ่มต้นเป็นภาษาอังกฤษ
$lang = $_SESSION['lang'] ?? 'th';

// โหลดไฟล์ภาษาตามการเลือกของผู้ใช้
$translations = include("lang/lang_{$lang}.php");
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $translations['login_title']; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
    <style>
    /* ปุ่มเปลี่ยนภาษาให้ติดขวาบน */
    .language-switch {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .language-switch img {
        width: 30px;
        height: 30px;
        cursor: pointer;
    }
    </style>
</head>

<body>
    <!-- ปุ่มเลือกภาษาเป็นธงชาติ -->
    <div class="language-switch">
        <a href="?lang=en"><img src="./lang/united-states.png" alt="English"></a>
        <a href="?lang=th"><img src="./lang/thailand.png" alt="ภาษาไทย"></a>
    </div>

    <div class="login-container">
        <h2><?php echo $translations['brand_text']; ?></h2>
        <h3><?php echo $translations['login']; ?></h3>
        <form class="login-form" action="login.php" method="post">
            <label for="username"><?php echo $translations['username']; ?>:</label>
            <input class="textuser" type="text" id="username" name="username"
                placeholder="<?php echo $translations['enter_username']; ?>" required>

            <label for="password"><?php echo $translations['password']; ?>:</label>
            <div class="password-container">
                <input class="textpass" type="password" id="password" name="password"
                    placeholder="<?php echo $translations['enter_password']; ?>" required>
                <button type="button" id="showPasswordButton" onclick="togglePassword()" style="padding: 10px">
                    <i class="fas fa-eye" id="passwordIcon"></i>
                </button>
            </div><br>

            <input class="button" type="submit" value="<?php echo $translations['login_button']; ?>"><br>
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
                alert('<?php echo $translations['invalid_credentials']; ?>');
            }
        };
        </script>
    </div>
</body>

</html>