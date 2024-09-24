<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_maidmanage";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id) {
    $sql = "SELECT DISTINCT floor_id, floor_name FROM floors WHERE user_id = $user_id";
    $result = $conn->query($sql);
    
    $floors = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $floors[] = $row;
        }
    }
    echo json_encode($floors);
} else {
    echo json_encode([]);
}

$conn->close();
?>
