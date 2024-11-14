<?php

session_start();

// Handle user registration
if (isset($_POST['registerbtn'])) {
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
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = sha1($_POST['password']); // Hash the password using SHA-1
    $role_id = $_POST['role_id'];
    $status_id = $_POST['status_id'];

    // ตรวจสอบ username ซ้ำ
    $check_username_query = "SELECT * FROM users WHERE username = ?";
    $check_stmt = $connection->prepare($check_username_query);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // กรณี username ซ้ำกัน
        $_SESSION['status'] = "Username already exists!";
        $_SESSION['status_code'] = "error";
        
        // นำกลับไปยังหน้า register.php หรือหน้าอื่นที่ต้องการ
        header('Location: users.php');
        exit();
    } else {
        // ใช้ Prepared Statements ในการเพิ่มข้อมูล
        $stmt = $connection->prepare("INSERT INTO users (fullname, username, password, role_id, status_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fullname, $username, $password, $role_id, $status_id);

        if ($stmt->execute()) {
            $_SESSION['status'] = "User added successfully!";
            $_SESSION['status_code'] = "success";
        } else {
            $_SESSION['status'] = "Failed to add user: " . $stmt->error;
            $_SESSION['status_code'] = "error";
        }

        // ปิด Prepared Statements
        $stmt->close();
    }

    // ปิดการเชื่อมต่อ
    $check_stmt->close();
    $connection->close();

    // นำกลับไปยังหน้า users.php
    header('Location: users.php');
    exit();
}



// Handle user deletion
if (isset($_POST['deleteUserId'])) {
    $servername = "db"; // Use the service name 'db' defined in docker-compose
    $username = "user"; // User defined in docker-compose
    $password = "user_password"; // Password defined in docker-compose
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
    $servername = "db"; // Use the service name 'db' defined in docker-compose
    $username = "user"; // User defined in docker-compose
    $password = "user_password"; // Password defined in docker-compose
    $dbname = "project_maidmanage";

    // สร้างการเชื่อมต่อฐานข้อมูล
    $connection = new mysqli($servername, $username, $password, $dbname);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $user_id = $_POST['user_id'];
    $task_date = $_POST['task_date']; // ใช้วันที่เดียวสำหรับ start และ end
    $floor_ids = $_POST['floor_id'];
    $room_ids = isset($_POST['room_id']) ? $_POST['room_id'] : []; // ตรวจสอบว่ามีการส่ง room_id มาหรือไม่
    $status_id = !empty($_POST['status_id']) ? $_POST['status_id'] : null;
    $toilet_status_id = !empty($_POST['toilet_status_id']) ? $_POST['toilet_status_id'] : null;

    foreach ($floor_ids as $floor_id) {
        if (isset($room_ids[$floor_id]) && !empty($room_ids[$floor_id])) {
            // กรณีที่เลือกห้องในแต่ละชั้น
            foreach ($room_ids[$floor_id] as $room_id) {
                if (!empty($room_id)) {
                    // ตรวจสอบว่าห้องที่เลือกมีอยู่ในฐานข้อมูลหรือไม่
                    $roomCheckQuery = "SELECT room_id FROM room WHERE room_id = ?";
                    $stmt = $connection->prepare($roomCheckQuery);
                    $stmt->bind_param('i', $room_id);
                    $stmt->execute();
                    $roomCheckResult = $stmt->get_result();

                    if ($roomCheckResult->num_rows > 0) {
                        // บันทึกข้อมูล task พร้อม room_id ที่ถูกต้อง
                        $query = "INSERT INTO task (user_id, start_date, end_date, floor_id, room_id, status_id, toilet_status_id) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $connection->prepare($query);
                        $stmt->bind_param('issiiii', $user_id, $task_date, $task_date, $floor_id, $room_id, $status_id, $toilet_status_id);

                        if ($stmt->execute()) {
                            $_SESSION['status'] = "Task added successfully!";
                            $_SESSION['status_code'] = "success";
                        } else {
                            $_SESSION['status'] = "Failed to add task: " . $stmt->error;
                            $_SESSION['status_code'] = "error";
                        }
                    } else {
                        $_SESSION['status'] = "Room ID $room_id does not exist.";
                        $_SESSION['status_code'] = "error";
                    }
                }
            }
        } else {
            // กรณีที่ไม่ได้เลือกห้อง ให้ใช้ room_id เป็น NULL
            $query = "INSERT INTO task (user_id, start_date, end_date, floor_id, room_id, status_id, toilet_status_id) 
                      VALUES (?, ?, ?, ?, NULL, ?, ?)";
            $stmt = $connection->prepare($query);
            $stmt->bind_param('issiii', $user_id, $task_date, $task_date, $floor_id, $status_id, $toilet_status_id);

            if ($stmt->execute()) {
                $_SESSION['status'] = "Task added successfully without room!";
                $_SESSION['status_code'] = "success";
            } else {
                $_SESSION['status'] = "Failed to add task without room: " . $stmt->error;
                $_SESSION['status_code'] = "error";
            }
        }
    }

    // ปิดการเชื่อมต่อฐานข้อมูล
    $connection->close();

    // Redirect ไปหน้า main.php
    header('Location: main.php');
    exit();
}




if (isset($_POST['send_task_btn'])) {
    // Connect to the database
    $servername = "db"; // Use the service name 'db' defined in docker-compose
    $username = "user"; // User defined in docker-compose
    $password = "user_password"; // Password defined in docker-compose
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

if (isset($_POST['reset_task_btn'])) {

    // Connect to the database
    $servername = "db"; // Use the service name 'db' defined in docker-compose
    $username = "user"; // User defined in docker-compose
    $password = "user_password"; // Password defined in docker-compose
    $dbname = "project_maidmanage";

    $connection = new mysqli($servername, $username, $password, $dbname);

    $task_id = $_POST['task_id'];
    $status_id = $_POST['status_id'];
    $toilet_status_id = $_POST['toilet_status_id'];

    $query = "UPDATE task SET status_id = '$status_id', toilet_status_id = '$toilet_status_id' WHERE task_id = '$task_id'";
    mysqli_query($connection, $query);
    header("Location: tables.php");  
    exit();
}

?>