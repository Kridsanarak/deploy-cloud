<?php
session_start();
session_unset(); // ล้างค่า session ทั้งหมด
session_destroy(); // ทำลาย session

// ลบค่า cookie โดยการตั้งค่า expiration ให้เป็นอดีต
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/'); // ลบ cookie ของ session
}

// Redirect ไปหน้า login หลังจาก logout
header("Location: index.php");
exit();
?>
