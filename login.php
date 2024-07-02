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

        // Update lasttime_login field
        $userId = $row['user_id'];
        $currentTime = date('Y-m-d H:i:s'); // Get current date and time
        $updateSql = "UPDATE users SET lasttime_login = ? WHERE user_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("si", $currentTime, $userId);
        $updateStmt->execute();

        // Check user role and redirect accordingly
        if ($row['role'] == 'maid') {
            header("Location: maid.php"); // Redirect to maid page
        } else if ($row['role'] == 'admin'){
            header("Location: main.php"); // Redirect to dashboard or profile page
        } else {
            header("Location: headmaid.php"); // Redirect to head maid page
        }
        
        exit(); // Ensure no further code execution after redirection
    } else {
        // Invalid credentials
        header("Location: index.php?login=fail");
        exit();
    }
}
?>
