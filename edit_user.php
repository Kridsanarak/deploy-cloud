<?php
session_start();

if (isset($_POST['edit_user_btn'])) {
    // เชื่อมต่อฐานข้อมูล
    $servername = "db";
    $username = "user";
    $password = "user_password";
    $dbname = "project_maidmanage";

    $connection = new mysqli($servername, $username, $password, $dbname);

    // ตรวจสอบการเชื่อมต่อ
    if ($connection->connect_error) {
        die("การเชื่อมต่อล้มเหลว: " . $connection->connect_error);
    }

    // รับค่าจากฟอร์ม
    $user_id = $_POST['user_id'];
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = sha1($_POST['password']);
    $role_id = $_POST['role_id'];
    $status_id = $_POST['status_id'];

    // ตรวจสอบว่า username ที่กรอกมาใหม่ซ้ำหรือไม่ (ไม่นับ user_id ปัจจุบัน)
    $check_query = $connection->prepare("SELECT * FROM users WHERE username = ? AND user_id != ?");
    $check_query->bind_param("si", $username, $user_id);
    $check_query->execute();
    $result = $check_query->get_result();

    if ($result->num_rows > 0) {
        // ถ้ามี username ซ้ำ ให้ตั้งค่า session สำหรับแจ้งเตือนและเก็บข้อมูลฟอร์ม
        $_SESSION['error'] = "Username already exists!";
        $_SESSION['form_data'] = $_POST; // เก็บข้อมูลที่กรอกไว้ใน session
    } else {
        // ถ้าไม่มี username นี้ในระบบ ทำการอัปเดตข้อมูลผู้ใช้
        $stmt = $connection->prepare("UPDATE users SET fullname = ?, username = ?, password = ?, role_id = ?, status_id = ? WHERE user_id = ?");
        $stmt->bind_param("sssssi", $fullname, $username, $password, $role_id, $status_id, $user_id);

        if ($stmt->execute()) {
            $_SESSION['status'] = "User updated successfully!";
            $_SESSION['status_code'] = "success";
        } else {
            $_SESSION['status'] = "Failed to update user: " . $stmt->error;
            $_SESSION['status_code'] = "error";
        }

        $stmt->close();
    }

    $check_query->close();
    $connection->close();

    // นำกลับไปยังหน้า users.php
    header('Location: users.php');
    exit();
}

?>
