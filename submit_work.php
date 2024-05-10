<?php
session_start(); // เริ่ม session

// ตรวจสอบว่ามีการส่งค่าจากแบบฟอร์มมาหรือไม่
if (isset($_POST['submit_work_btn'])) {
    // เชื่อมต่อกับฐานข้อมูล
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "project";

    $connection = new mysqli($servername, $username, $password, $dbname);

    // ตรวจสอบการเชื่อมต่อ
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // รับข้อมูลจากแบบฟอร์ม
    $task_id = $_POST['task_id'];
    $room_status = $_POST['room_status'];
    $toilet_status = $_POST['toilet_status'];

    // ตรวจสอบว่ามีการอัปโหลดไฟล์รูปภาพหรือไม่
    if (isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target_dir = "upload/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    } else {
        $image = ''; // ถ้าไม่มีการอัปโหลดรูปภาพให้กำหนดค่าเป็นช่องว่าง
    }

    // สร้างคำสั่ง SQL เพื่ออัปเดตข้อมูล
    $sql = "UPDATE task SET room_status='$room_status', toilet_status='$toilet_status', image='$image' WHERE task_id=$task_id";

    if ($connection->query($sql) === TRUE) {
        $_SESSION['status'] = "Task updated successfully!";
        $_SESSION['status_code'] = "success";
    } else {
        $_SESSION['status'] = "Failed to update task";
        $_SESSION['status_code'] = "error";
    }

    $connection->close(); // ปิดการเชื่อมต่อฐานข้อมูล

    header('Location: table_user.php'); // เปลี่ยนเส้นทางใหม่
}
?>