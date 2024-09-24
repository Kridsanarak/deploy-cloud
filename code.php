<?php

session_start();

// Handle user registration
if (isset($_POST['registerbtn'])) {
    // เชื่อมต่อฐานข้อมูล
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "project_maidmanage";

    $connection = new mysqli($servername, $username, $password, $dbname);

    // ตรวจสอบการเชื่อมต่อ
    if ($connection->connect_error) {
        die("การเชื่อมต่อล้มเหลว: " . $connection->connect_error);
    }

    // รับค่าจากฟอร์ม
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = sha1($_POST['password']); // Hash the password using SHA-1
    $role_id = $_POST['role_id'];
    $status_id = $_POST['status_id'];

    // แปลง timestamp เป็นรูปแบบวันที่และเวลาที่ต้องการ
    $formatted_date = date("Y-m-d H:i:s", $timestamp);

    // ดึงค่า timestamp ปัจจุบัน
    $timestamp = time();

    // ใช้ Prepared Statements
    $stmt = $connection->prepare("INSERT INTO users (fullname, username, password, role_id, status_id, timestamp) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $fullname, $username, $password, $role_id, $status_id, $timestamp);

    if ($stmt->execute()) {
        $_SESSION['status'] = "User added successfully!";
        $_SESSION['status_code'] = "success";
    } else {
        $_SESSION['status'] = "Failed to add user: " . $stmt->error;
        $_SESSION['status_code'] = "error";
    }

    // ปิดการเชื่อมต่อ
    $stmt->close();
    $connection->close();

    // นำกลับไปยังหน้า users.php
    header('Location: users.php');
}


// Handle user deletion
if (isset($_POST['deleteUserId'])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "project_maidmanage";

    $connection = new mysqli($servername, $username, $password, $dbname);

    // ตรวจสอบการเชื่อมต่อ
    if ($connection->connect_error) {
        die("การเชื่อมต่อล้มเหลว: " . $connection->connect_error);
    }
    $userId = $_POST['deleteUserId'];

    // First, delete all tasks related to this user
    $deleteTasksQuery = "DELETE FROM task WHERE user_id=$userId";
    if ($connection->query($deleteTasksQuery) === TRUE) {
        // If task deletion is successful, delete the user
        $deleteUserQuery = "DELETE FROM users WHERE user_id=$userId";
        if ($connection->query($deleteUserQuery) === TRUE) {
            // กระทำหลังจากลบผู้ใช้งานสำเร็จ
            // ทำการ reset AUTO_INCREMENT
            $connection->query("ALTER TABLE users AUTO_INCREMENT = 1");

            $_SESSION['status'] = "User deleted successfully!";
            $_SESSION['status_code'] = "success";
        } else {
            $_SESSION['status'] = "Error deleting user: " . $connection->error;
            $_SESSION['status_code'] = "error";
        }
    } else {
        $_SESSION['status'] = "Error deleting tasks: " . $connection->error;
        $_SESSION['status_code'] = "error";
    }

    // ปิดการเชื่อมต่อ
    $connection->close();

    // นำกลับไปยังหน้า users.php
    header('Location: users.php');
}


if (isset($_POST['add_task_btn'])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "project_maidmanage";

    $connection = new mysqli($servername, $username, $password, $dbname);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $user_id = $_POST['user_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $floor_id = $_POST['floor_id'];
    $room_id = !empty($_POST['room_id']) ? $_POST['room_id'] : null;
    $status_id = !empty($_POST['status_id']) ? $_POST['status_id'] : null;
    $toilet_gender_id = !empty($_POST['toilet_gender_id']) ? $_POST['toilet_gender_id'] : null;
    $toilet_status_id = !empty($_POST['toilet_status_id']) ? $_POST['toilet_status_id'] : null;

    // แปลงวันที่เริ่มต้นและวันที่สิ้นสุดเป็นออบเจ็กต์ DateTime
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $end = $end->modify('+1 day'); // รวมวันที่สิ้นสุดในช่วงเวลา

    // สร้าง DatePeriod โดยมีช่วงเวลาหนึ่งวัน
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($start, $interval, $end);

    foreach ($period as $date) {
        $current_date = $date->format('Y-m-d');

        // เพิ่มงานสำหรับแต่ละวันที่ในช่วงเวลา
        $query = "INSERT INTO task (user_id, start_date, end_date, floor_id, room_id, status_id, toilet_gender_id, toilet_status_id) 
                  VALUES ('$user_id', '$current_date', '$current_date', '$floor_id', " . ($room_id !== null ? "'$room_id'" : "NULL") . ", " . ($status_id !== null ? "'$status_id'" : "NULL") . ", " . ($toilet_gender_id !== null ? "'$toilet_gender_id'" : "NULL") . ", " . ($toilet_status_id !== null ? "'$toilet_status_id'" : "NULL") . ")";

        if (mysqli_query($connection, $query)) {
            $_SESSION['status'] = "Task added successfully!";
            $_SESSION['status_code'] = "success";
        } else {
            $_SESSION['status'] = "Failed to add task: " . mysqli_error($connection);
            $_SESSION['status_code'] = "error";
        }
    }

    // ปิดการเชื่อมต่อ
    $connection->close();

    // เปลี่ยนเส้นทางกลับไปที่หน้าแรก
    header('Location: main.php');
}

if (isset($_POST['send_task_btn'])) {
    // Connect to the database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "project_maidmanage";

    $connection = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Get data from the form
    $task_id = $_POST['task_id'];
    $status_id = !empty($_POST['status_id']) ? $_POST['status_id'] : null; 
    $toilet_status_id = !empty($_POST['toilet_status_id']) ? $_POST['toilet_status_id'] : null; 

    // Initialize image variable
    $image = '';

    // Check if an image has been uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image']['name'];
        $target_dir = "upload/"; // Ensure this directory exists
        $target_file = $target_dir . basename($image);
        
        // Move uploaded file to target directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // File uploaded successfully
        } else {
            // Handle file upload error
            $_SESSION['status'] = "Failed to upload image.";
            $_SESSION['status_code'] = "error";
            header('Location: send.php'); // Redirect
            exit;
        }
    }

    // SQL query to update the task
    $sql = "UPDATE task SET status_id='$status_id', toilet_status_id='$toilet_status_id'";
    
    // Include image in the query if uploaded
    if (!empty($image)) {
        $sql .= ", image='$image'"; // Add image to the update
    }
    
    $sql .= " WHERE task_id=$task_id"; // Complete the query

    // Execute the query and check for success
    if ($connection->query($sql) === TRUE) {
        $_SESSION['status'] = "Task updated successfully!";
        $_SESSION['status_code'] = "success";
    } else {
        $_SESSION['status'] = "Failed to update task: " . $connection->error;
        $_SESSION['status_code'] = "error";
    }

    $connection->close(); // Close database connection

    header('Location: send.php'); // Redirect
}


?>