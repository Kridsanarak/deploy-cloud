<?php
session_start();

if (isset($_POST['edit_user_btn'])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "project";

    // สร้างการเชื่อมต่อกับฐานข้อมูล
    $connection = new mysqli($servername, $username, $password, $dbname);

    // ตรวจสอบการเชื่อมต่อ
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // รับค่าจากฟอร์มและล้างข้อมูลเพื่อป้องกันการโจมตี XSS
    $userId = intval($_POST['user_id']); // ใช้ intval() เพื่อให้แน่ใจว่าเป็นตัวเลข
    $fullname = htmlspecialchars($_POST['fullname']);
    $username = htmlspecialchars($_POST['username']);
    $password = sha1($_POST['password']); // เข้ารหัสรหัสผ่าน
    $role = intval($_POST['role_id']); // ใช้ intval() เพื่อให้แน่ใจว่าเป็นตัวเลข
    $status = intval($_POST['status_id']); // ใช้ intval() เพื่อให้แน่ใจว่าเป็นตัวเลข

    // แก้ไขชื่อคอลัมน์ให้ตรงกับตาราง
    $sql = "UPDATE users SET fullname=?, username=?, password=?, role_id=?, status_id=? WHERE user_id=?";
    
    $stmt = $connection->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $connection->error);
    }

    // ผูกตัวแปรกับคำสั่ง SQL
    $stmt->bind_param("sssiii", $fullname, $username, $password, $role, $status, $userId);

    // รันคำสั่ง SQL
    if ($stmt->execute()) {
        $_SESSION['status'] = "User updated successfully!";
        $_SESSION['status_code'] = "success";
    } else {
        $_SESSION['status'] = "Failed to update user: " . $stmt->error;
        $_SESSION['status_code'] = "error";
    }

    // ปิดการเชื่อมต่อและคำสั่ง
    $stmt->close();
    $connection->close();

    // เปลี่ยนเส้นทางไปยังหน้าผู้ใช้
    header('Location: users.php');
    exit();
}
?>
