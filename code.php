<?php

session_start();

// ตรวจสอบว่ามีการกำหนดค่าใน $_POST['registerbtn'] หรือไม่
if(isset($_POST['registerbtn'])) {
    // เชื่อมต่อฐานข้อมูล
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "project";

    $connection = new mysqli($servername, $username, $password, $dbname);

    // ตรวจสอบการเชื่อมต่อ
    if ($connection->connect_error) {
        die("การเชื่อมต่อล้มเหลว: " . $connection->connect_error);
    }

    // รับค่าจากฟอร์ม
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // คำสั่ง SQL เพื่อเพิ่มข้อมูลผู้ใช้
    $query = "INSERT INTO users (fullname, username, password, role) VALUES ('$fullname', '$username', '$password', '$role')";

    // ทำการ query คำสั่ง SQL
    if(mysqli_query($connection, $query)) {
        $_SESSION['status'] = "User added successfully!";
        $_SESSION['status_code'] = "success";
    } else {
        $_SESSION['status'] = "Failed to add user";
        $_SESSION['status_code'] = "error";
    }

    // ปิดการเชื่อมต่อ
    $connection->close();

    // นำกลับไปยังหน้า users.php
    header('Location: users.php');
}

if(isset($_POST['deleteUserId'])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "project";

    $connection = new mysqli($servername, $username, $password, $dbname);

    // ตรวจสอบการเชื่อมต่อ
    if ($connection->connect_error) {
        die("การเชื่อมต่อล้มเหลว: " . $connection->connect_error);
    }
    $userId = $_POST['deleteUserId'];
    
    // สร้างคำสั่ง SQL เพื่อลบผู้ใช้งาน
    $sql = "DELETE FROM users WHERE user_id=$userId";

    if ($connection->query($sql) === TRUE) {
        // กระทำหลังจากลบผู้ใช้งานสำเร็จ
        // ทำการ reset AUTO_INCREMENT
        $connection->query("ALTER TABLE users AUTO_INCREMENT = 1");

        echo "User deleted successfully";
    } else {
        echo "Error deleting user: " . $connection->error;
    }
}


?>
