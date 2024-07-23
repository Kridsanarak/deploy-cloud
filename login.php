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

    // ตรวจสอบความถูกต้องของข้อมูลผู้ใช้
    $sql = "SELECT user_id, username, fullname, role_id FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // เข้าสู่ระบบสำเร็จ
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['full_name'] = $row['fullname'];
        $_SESSION['role_id'] = $row['role_id']; // เก็บค่าบทบาทของผู้ใช้ใน session

        $userId = $row['user_id'];
        $dateTime = new DateTime("now", new DateTimeZone('Asia/Bangkok')); // Create DateTime object with Asia/Bangkok timezone
        $currentTime = $dateTime->format('Y-m-d H:i:s'); // Get current date and time in specified format
        $updateSql = "UPDATE users SET timestamp = ? WHERE user_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("si", $currentTime, $userId);
        $updateStmt->execute();

        // เปลี่ยนหน้าไปยัง main.php หลังจากเข้าสู่ระบบสำเร็จ
        header("Location: main.php");
        exit(); // หยุดการทำงานของโค้ดหลังจากเปลี่ยนหน้า
    } else {
        // ข้อมูลไม่ถูกต้อง
        header("Location: index.php?login=fail");
        exit();
    }
}
?>
