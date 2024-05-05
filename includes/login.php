<?php
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

// รับข้อมูลจากฟอร์ม
$user = $_POST['username'];
$pass = $_POST['password'];

// ค้นหาผู้ใช้ในฐานข้อมูล
$query = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
$result = $conn->query($query);

// ตรวจสอบผลลัพธ์
if ($result->num_rows > 0) {
    // Login สำเร็จ
    echo json_encode(["success" => true]);
    
    // อัปเดต timestamp ใน lasttime_login
    $updateQuery = "UPDATE users SET lasttime_login = CURRENT_TIMESTAMP WHERE username = '$user'";
    $conn->query($updateQuery);
    
    // ทำการเข้าสู่ระบบ, เช่น การกำหนด Session, Redirect ไปหน้าหลังจาก Login
} else {
    // Login ไม่สำเร็จ
    echo json_encode(["success" => false]);
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
