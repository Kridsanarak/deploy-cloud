<?php

$servername = "db"; // Use the service name 'db' defined in docker-compose
$username = "user"; // User defined in docker-compose
$password = "user_password"; // Password defined in docker-compose
$dbname = "project_maidmanage";

// Establishing connection to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user_id from session
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Build SQL query based on role
if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 3) {
    // For role_id 3, filter tasks for the current user
    $sql = "SELECT task_id, start_date, end_date, floor_id FROM task WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
} else {
    // For other roles, get all tasks
    $sql = "SELECT task_id, start_date, end_date, floor_id FROM task";
    $stmt = $conn->prepare($sql);
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// Check if there are results
if ($result->num_rows > 0) {
    $tasks = array();
    // Loop through results and build event array
    while ($row = $result->fetch_assoc()) {
        $event = array();
        $event['id'] = $row['task_id'];
        $event['start'] = $row['start_date'];
        $event['end'] = $row['end_date'];
        $event['floor_id'] = $row['floor_id'];

        array_push($tasks, $event);
    }
    // Output tasks in JSON format
    echo json_encode($tasks);
} else {
    // No tasks found
    echo json_encode(array());
}

// Close connection
$conn->close();
?>
