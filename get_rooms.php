<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_maidmanage";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$floor_id = isset($_GET['floor_id']) ? intval($_GET['floor_id']) : 0;

if ($floor_id) {
    $sql = "SELECT room_id, room_name FROM room WHERE floor_id = $floor_id";
    $result = $conn->query($sql);
    
    $rooms = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rooms[] = $row;
        }
    }
    echo json_encode($rooms);
} else {
    echo json_encode([]);
}

$conn->close();
?>
