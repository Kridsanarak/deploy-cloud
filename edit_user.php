<?php
session_start();

if (isset($_POST['edit_user_btn'])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "project";

    $connection = new mysqli($servername, $username, $password, $dbname);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $userId = $_POST['user_id'];
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    $sql = "UPDATE users SET fullname='$fullname', username='$username', password='$password', role='$role', status='$status' WHERE user_id=$userId";

    if ($connection->query($sql) === TRUE) {
        $_SESSION['status'] = "User updated successfully!";
        $_SESSION['status_code'] = "success";
    } else {
        $_SESSION['status'] = "Failed to update user";
        $_SESSION['status_code'] = "error";
    }

    $connection->close();

    header('Location: users.php');
}
?>
