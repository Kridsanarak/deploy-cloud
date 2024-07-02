<?php
session_start();
// เชื่อมต่อกับ MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = sha1($_POST['password']);

    // Validate user credentials
    $sql = "SELECT user_id, username, fullname, role FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Login successful
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['full_name'] = $row['fullname'];
        $_SESSION['role'] = $row['role']; // เพิ่ม session role ที่เก็บค่าบทบาทของผู้ใช้

        // Update lasttime_login field
        $userId = $row['user_id'];
        $currentTime = date('Y-m-d H:i:s'); // Get current date and time
        $updateSql = "UPDATE users SET lasttime_login = ? WHERE user_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("si", $currentTime, $userId);
        $updateStmt->execute();

        // Redirect to main.php after successful login
        header("Location: main.php");
        exit(); // Ensure no further code execution after redirection
    } else {
        // Invalid credentials
        header("Location: index.php?login=fail");
        exit();
    }

}
?>
'